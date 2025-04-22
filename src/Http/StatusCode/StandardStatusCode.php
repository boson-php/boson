<?php

declare(strict_types=1);

namespace Boson\Http\StatusCode;

use Boson\Http\StatusCodeInterface;

/**
 * @template T of int
 * @template-implements StatusCodeInterface<T>
 */
abstract readonly class StandardStatusCode implements StatusCodeInterface
{
    use StatusCodeValueObjectImpl;

    public function __construct(
        /**
         * @var T
         */
        public int $code,
        public string $reason,
        public CategoryInterface $category,
    ) {}
}
