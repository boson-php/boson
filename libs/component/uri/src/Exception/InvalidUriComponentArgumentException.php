<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Exception;

use Boson\Contracts\Uri\Exception\InvalidArgumentExceptionInterface;

abstract class InvalidUriComponentArgumentException extends UriComponentException implements
    InvalidArgumentExceptionInterface
{
    final public const int ERROR_CODE_IN_STRINGABLE = 0x01;
    final public const int ERROR_CODE_INVALID = 0x02;
    final public const int ERROR_CODE_EMPTY = 0x03;

    final public function __construct(string $message, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return non-empty-string
     */
    abstract public static function getComponentName(): string;

    public static function becauseStringableErrorOccurs(\Throwable $e): static
    {
        $message = \vsprintf('An error occurred while casting URI %s to string: %s', [
            static::getComponentName(),
            $e->getMessage(),
        ]);

        return new static($message, self::ERROR_CODE_IN_STRINGABLE, $e);
    }

    public static function becauseComponentMustBe(string $expected, mixed $given, ?\Throwable $prev = null): static
    {
        $message = \vsprintf('An URI %s must be %s, but %s given', [
            static::getComponentName(),
            $expected,
            self::getType($given),
        ]);

        return new static($message, self::ERROR_CODE_INVALID, $prev);
    }

    public static function becauseComponentIsEmpty(?\Throwable $prev = null): static
    {
        $message = \vsprintf('An URI %s cannot be empty', [
            static::getComponentName(),
        ]);

        return new static($message, self::ERROR_CODE_EMPTY, $prev);
    }
}
