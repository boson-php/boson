<?php

declare(strict_types=1);

namespace Boson\Http\StatusCode\Category;

use Boson\Http\StatusCode\Category;
use Boson\Shared\ValueObject\IntValueObjectInterface;

/**
 * An implementation of the builtin HTTP categories.
 *
 * @template-implements IntValueObjectInterface<int<100, 599>>
 */
final readonly class HttpCategory extends StandardCategory implements
    IntValueObjectInterface
{
    /**
     * @param non-empty-string $name
     */
    public function __construct(
        string $name,
        /**
         * @var int<100, 599>
         */
        public int $range,
    ) {
        parent::__construct($name);
    }

    /**
     * @api
     *
     * @return ($code is int<100, 599> ? self : null)
     */
    public static function tryFromStatusCodeValue(int $code): ?self
    {
        return match (true) {
            $code < 100 => null,
            $code < 200 => Category::Informational,
            $code < 300 => Category::Successful,
            $code < 400 => Category::Redirection,
            $code < 500 => Category::ClientError,
            $code < 600 => Category::ServerError,
            default => null,
        };
    }

    /**
     * @api
     *
     * @return ($code is int<100, 599> ? self : never)
     */
    public static function fromStatusCodeValue(int $code): self
    {
        return self::tryFromStatusCodeValue($code)
            ?? ($code < 100
                ? throw new \OutOfRangeException('HTTP status code cannot be less than 100')
                : throw new \OutOfRangeException('HTTP status code cannot be greater than 599'));
    }

    public function toInteger(): int
    {
        return $this->range;
    }

    public function equals(mixed $object): bool
    {
        return parent::equals($object)
            /** @phpstan-ignore-next-line : The "$object" is self after "equals" execution */
            && $this->range === $object->range;
    }
}
