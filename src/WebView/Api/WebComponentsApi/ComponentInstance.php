<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi;

use Boson\WebView\Api\WebComponentsApi\Metadata\WebComponentMetadata;

/**
 * @template TComponent of object
 */
final readonly class ComponentInstance
{
    public function __construct(
        /**
         * @var TComponent
         */
        public object $instance,
        /**
         * @var WebComponentMetadata<TComponent>
         */
        public WebComponentMetadata $meta,
    ) {}
}
