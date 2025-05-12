<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata\Reader;

use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\Attributes\AttributesReaderInterface;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\ClassName\ClassNameReaderInterface;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\ConnectMethod\ConnectMethodReaderInterface;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\DisconnectMethod\DisconnectMethodReaderInterface;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\TagName\TagNameReaderInterface;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\Template\TemplateReaderInterface;
use Boson\WebView\Api\WebComponentsApi\Metadata\WebComponentMetadata;

abstract readonly class CompositeWebComponentMetadataReader implements WebComponentMetadataReaderInterface
{
    public function __construct(
        private TagNameReaderInterface $tagNameReader,
        private ClassNameReaderInterface $classNameReader,
        private AttributesReaderInterface $attributesReader,
        private TemplateReaderInterface $templateReader,
        private ConnectMethodReaderInterface $connectMethodReader,
        private DisconnectMethodReaderInterface $disconnectMethodReader,
    ) {}

    public function getMetadata(string $component): WebComponentMetadata
    {
        return new WebComponentMetadata(
            component: $component,
            className: $this->classNameReader->getClassName($component),
            tagName: $this->tagNameReader->getTagName($component),
            template: $this->templateReader->findTemplate($component),
            attributes: $this->attributesReader->getAttributes($component),
            onConnect: $this->connectMethodReader->findConnectMethod($component),
            onDisconnect: $this->disconnectMethodReader->findDisconnectMethod($component),
        );
    }
}
