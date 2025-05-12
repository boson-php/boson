<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata;

/**
 * @template TComponent of object
 */
final readonly class WebComponentMetadata
{
    /**
     * Contain `[<attribute> => WebComponentAttributeMetadata]` hash-map index.
     *
     * @var array<non-empty-string, WebComponentAttributeMetadata>
     */
    private array $attributesToMetadata;

    /**
     * Contain `[<property> => WebComponentAttributeMetadata]` hash-map index.
     *
     * @var array<non-empty-string, WebComponentAttributeMetadata>
     */
    private array $propertiesToMetadata;

    /**
     * @param iterable<mixed, WebComponentAttributeMetadata> $attributes
     */
    public function __construct(
        /**
         * @var class-string<TComponent>
         */
        public string $component,
        /**
         * @var non-empty-string
         */
        public string $className,
        /**
         * @var non-empty-string
         */
        public string $tagName,
        public ?WebComponentTemplateMetadata $template = null,
        iterable $attributes = [],
        /**
         * @var non-empty-string|null
         */
        public ?string $onConnect = null,
        /**
         * @var non-empty-string|null
         */
        public ?string $onDisconnect = null,
    ) {
        $propertiesToMetadata = $attributesToMetadata = [];

        foreach ($attributes as $meta) {
            $propertiesToMetadata[$meta->property] = $meta;
            $attributesToMetadata[$meta->attribute] = $meta;
        }

        $this->propertiesToMetadata = $propertiesToMetadata;
        $this->attributesToMetadata = $propertiesToMetadata;
    }

    /**
     * @return list<non-empty-string>
     */
    public function getAttributeNames(): array
    {
        return \array_keys($this->attributesToMetadata);
    }

    /**
     * @api
     *
     * @param non-empty-string $attribute
     * @return non-empty-string|null
     */
    public function findPropertyByAttributeName(string $attribute): ?string
    {
        return $this->attributesToMetadata[$attribute]?->property;
    }

    /**
     * @return list<non-empty-string>
     */
    public function geetPropertyNames(): array
    {
        return \array_keys($this->propertiesToMetadata);
    }

    /**
     * @api
     *
     * @param non-empty-string $attribute
     * @return non-empty-string|null
     */
    public function findAttributeByPropertyName(string $property): ?string
    {
        return $this->propertiesToMetadata[$property]?->attribute;
    }
}
