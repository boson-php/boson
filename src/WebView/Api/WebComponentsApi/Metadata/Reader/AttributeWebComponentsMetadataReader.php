<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata\Reader;

use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\ClassName\AttributeClassNameReader;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\ClassName\ClassNameReaderInterface;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\ClassName\NativeClassNameReader;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\Attributes\AttributeAttributesReader;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\Attributes\AttributesReaderInterface;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\ConnectMethod\AttributeConnectMethodReader;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\ConnectMethod\ConnectMethodReaderInterface;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\ConnectMethod\NativeConnectMethodReader;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\DisconnectMethod\AttributeDisconnectMethodReader;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\DisconnectMethod\DisconnectMethodReaderInterface;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\DisconnectMethod\NativeDisconnectMethodReader;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\TagName\AttributeTagNameReader;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\TagName\NativeTagNameReader;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\TagName\TagNameReaderInterface;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\Template\AttributeTemplateReader;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\Template\NativeTemplateReader;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\Template\TemplateReaderInterface;

final readonly class AttributeWebComponentsMetadataReader extends CompositeWebComponentMetadataReader
{
    public function __construct(
        TagNameReaderInterface $tagNameReader = new AttributeTagNameReader(
            delegate: new NativeTagNameReader(),
        ),
        ClassNameReaderInterface $classNameReader = new AttributeClassNameReader(
            delegate: new NativeClassNameReader(),
        ),
        AttributesReaderInterface $attributesReader = new AttributeAttributesReader(),
        TemplateReaderInterface $templateReader = new AttributeTemplateReader(
            delegate: new NativeTemplateReader(),
        ),
        ConnectMethodReaderInterface $connectMethodReader = new AttributeConnectMethodReader(
            delegate: new NativeConnectMethodReader(),
        ),
        DisconnectMethodReaderInterface $disconnectMethodReader = new AttributeDisconnectMethodReader(
            delegate: new NativeDisconnectMethodReader(),
        ),
    ) {
        parent::__construct(
            tagNameReader: $tagNameReader,
            classNameReader: $classNameReader,
            attributesReader: $attributesReader,
            templateReader: $templateReader,
            connectMethodReader: $connectMethodReader,
            disconnectMethodReader: $disconnectMethodReader,
        );
    }
}
