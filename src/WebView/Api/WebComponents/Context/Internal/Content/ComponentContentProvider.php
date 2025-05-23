<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Context\Internal\Content;

use Boson\WebView\Api\Data\SyncDataRetrieverInterface;
use Boson\WebView\Api\WebComponents\Context\ContentProviderInterface;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView\Api\WebComponents
 */
class ComponentContentProvider implements ContentProviderInterface
{
    public string $html {
        /** @phpstan-ignore-next-line : An innerHTML will return string */
        get => (string) $this->data->get('this.innerHTML');
    }

    public string $text {
        /** @phpstan-ignore-next-line : A textContent will return string|null */
        get => (string) $this->data->get('this.textContent');
    }

    public function __construct(
        private readonly SyncDataRetrieverInterface $data,
    ) {}
}
