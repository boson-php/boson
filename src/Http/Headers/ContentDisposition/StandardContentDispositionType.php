<?php

declare(strict_types=1);

namespace Boson\Http\Headers\ContentDisposition;

final readonly class StandardContentDispositionType implements ContentDispositionTypeInterface
{
    public function __construct(
        /**
         * @var non-empty-lowercase-string
         */
        public string $value,
    ) {}

    public function __toString(): string
    {
        return $this->value;
    }
}
