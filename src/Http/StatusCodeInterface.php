<?php

declare(strict_types=1);

namespace Boson\Http;

use Boson\Http\StatusCode\CategoryInterface;
use Boson\Shared\ValueObject\IntValueObjectInterface;

/**
 * @template T of int
 * @template-extends IntValueObjectInterface<T>
 */
interface StatusCodeInterface extends IntValueObjectInterface
{
    /**
     * Gets status code
     *
     * @var T
     */
    public int $code { get; }

    /**
     * Gets reason phrase message string of this status code
     */
    public string $reason { get; }

    /**
     * Gets category of this status code
     */
    public CategoryInterface $category { get; }
}
