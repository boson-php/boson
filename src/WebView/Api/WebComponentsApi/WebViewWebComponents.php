<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi;

use Boson\Dispatcher\EventDispatcherInterface;
use Boson\Internal\Saucer\LibSaucer;
use Boson\WebView\Api\WebComponentsApi\Exception\ComponentAlreadyDefinedExceptionWeb;
use Boson\WebView\Api\WebComponentsApi\Metadata\WebComponentMetadata;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\WebComponentMetadataReaderInterface;
use Boson\WebView\Api\WebComponentsApiInterface;
use Boson\WebView\Api\WebViewApi;
use Boson\WebView\Event\WebViewNavigating;
use Boson\WebView\WebView;

final class WebViewWebComponents extends WebViewApi implements WebComponentsApiInterface
{
    /**
     * A map containing a link between a tag name and a metadata object.
     *
     * @var array<non-empty-string, WebComponentMetadata>
     */
    private array $components = [];

    /**
     * List of loaded (instantiated) components.
     */
    private readonly WebViewComponentInstances $instances;

    /**
     * Component metadata reader.
     */
    private readonly WebComponentMetadataReaderInterface $meta;

    public function __construct(LibSaucer $api, WebView $webview, EventDispatcherInterface $dispatcher)
    {
        parent::__construct($api, $webview, $dispatcher);

        $this->meta = $this->webview->info->webComponents->metadataReader;

        $this->instances = new WebViewComponentInstances(
            instantiator: $this->webview->info->webComponents->instantiator,
            accessor: $this->webview->info->webComponents->propertyAccessor,
        );

        $this->registerDefaultEventListener();
        $this->registerDefaultFunctions();
    }

    private function registerDefaultEventListener(): void
    {
        $this->listen(WebViewNavigating::class, function () {
            $this->instances->destroyAll();
        });
    }

    private function registerDefaultFunctions(): void
    {
        $this->webview->bind('boson.components.created', $this->onCreated(...));
        $this->webview->bind('boson.components.connected', $this->onConnected(...));
        $this->webview->bind('boson.components.disconnected', $this->onDisconnected(...));
        $this->webview->bind('boson.components.attributeChanged', $this->onAttributeChanged(...));
    }

    private function onCreated(string $tag, string $id): void
    {
        $metadata = $this->components[$tag] ?? null;

        if ($metadata === null) {
            return;
        }

        $this->instances->create($id, $metadata);
    }

    private function onConnected(string $id): ?string
    {
        return $this->instances->notifyConnect($id);
    }

    private function onDisconnected(string $id): void
    {
        $this->instances->notifyDisconnect($id);
    }

    private function onAttributeChanged(string $id, string $name, ?string $value, ?string $previous): void
    {
        $this->instances->notifyAttributeChange($id, $name, $value, $previous);
    }

    /**
     * @param class-string $component
     */
    public function add(string $component): void
    {
        $metadata = $this->meta->getMetadata($component);

        if (isset($this->components[$metadata->tagName])) {
            throw ComponentAlreadyDefinedExceptionWeb::becauseComponentAlreadyDefined(
                tag: $metadata->tagName,
                component: $metadata->component,
            );
        }

        $this->webview->scripts->add($this->pack(
            meta: $this->components[$metadata->tagName] = $this->meta->getMetadata($component),
        ));
    }

    private function pack(WebComponentMetadata $meta): string
    {
        $attributes = \json_encode($meta->getAttributeNames());

        return <<<JS
            class {$meta->className} extends HTMLElement {
                #id;
                #internals;
                static observedAttributes = {$attributes};

                constructor() {
                    super();

                    this.#internals = this.attachInternals();
                    this.#id = window.boson.ids.generate();

                    window.boson.components.created("{$meta->tagName}", this.#id);
                    window.boson.components.instances[this.#id] = this;
                }

                connectedCallback() {
                    const self = this;

                    window.boson.components.connected(this.#id)
                        .then(function (value) {
                            if (value !== null) {
                                self.attachShadow({ mode: "open" })
                                    .innerHTML = value;
                            }
                        });
                }

                disconnectedCallback() {
                    delete window.boson.components.instances[this.#id];

                    window.boson.components.disconnected(this.#id);
                }

                attributeChangedCallback(name, oldValue, newValue) {
                    window.boson.components.attributeChanged(this.#id, name, newValue, oldValue);
                }
            }

            customElements.define("{$meta->tagName}", {$meta->className});
            JS;
    }
}
