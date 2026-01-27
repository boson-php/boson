<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Exception;

use Boson\Contracts\Uri\Exception\InvalidArgumentExceptionInterface;

/**
 * @phpstan-consistent-constructor
 */
abstract class InvalidUriComponentArgumentException extends UriComponentException implements
    InvalidArgumentExceptionInterface
{
    final public const int ERROR_CODE_IN_STRINGABLE = 0x01;
    final public const int ERROR_CODE_INVALID = 0x02;
    final public const int ERROR_CODE_EMPTY = 0x03;

    /**
     * @return non-empty-string
     */
    abstract public static function getComponentName(): string;

    final public static function becauseStringableErrorOccurs(\Throwable $e): static
    {
        assert(self::class !== static::class, 'Invalid static call context');

        $message = \vsprintf('An error occurred while casting URI %s to string: %s', [
            static::getComponentName(),
            $e->getMessage(),
        ]);

        /** @phpstan-ignore-next-line : The context is checked above in the assertion */
        return new static($message, self::ERROR_CODE_IN_STRINGABLE, $e);
    }

    final public static function becauseComponentMustBe(string $expected, mixed $given, ?\Throwable $prev = null): static
    {
        assert(self::class !== static::class, 'Invalid static call context');

        $message = \vsprintf('An URI %s must be %s, but %s given', [
            static::getComponentName(),
            $expected,
            self::getType($given),
        ]);

        /** @phpstan-ignore-next-line : The context is checked above in the assertion */
        return new static($message, self::ERROR_CODE_INVALID, $prev);
    }

    final public static function becauseComponentIsEmpty(?\Throwable $prev = null): static
    {
        assert(self::class !== static::class, 'Invalid static call context');

        $message = \vsprintf('An URI %s cannot be empty', [
            static::getComponentName(),
        ]);

        /** @phpstan-ignore-next-line : The context is checked above in the assertion */
        return new static($message, self::ERROR_CODE_EMPTY, $prev);
    }
}
