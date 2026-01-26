<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Exception;

use Boson\Contracts\Uri\Exception\InvalidArgumentExceptionInterface;

class InvalidUriComponentArgumentException extends UriComponentException implements
    InvalidArgumentExceptionInterface
{
    final public const int ERROR_CODE_IN_STRINGABLE = 0x01;
    final public const int ERROR_CODE_INVALID = 0x02;
    final public const int ERROR_CODE_EMPTY = 0x03;
    final public const int ERROR_CODE_LOGIC = 0x04;

    public static function becauseStringableErrorOccurs(\Throwable $e): self
    {
        $message = \sprintf('An error occurred while casting the URI component to string: %s', $e->getMessage());

        return new self($message, self::ERROR_CODE_IN_STRINGABLE, $e);
    }

    public static function becauseComponentMustBe(string $component, string $expected, mixed $given, ?\Throwable $e = null): self
    {
        $type = self::getType($given);

        $message = \sprintf('The "%s" URI component must be %s, but %s given', $component, $expected, $type);

        return new self($message, self::ERROR_CODE_INVALID, $e);
    }

    public static function becauseComponentIsEmpty(string $component, ?\Throwable $e = null): self
    {
        $message = \sprintf('The "%s" URI component cannot be empty', $component);

        return new self($message, self::ERROR_CODE_EMPTY, $e);
    }

    public static function becauseInvalidLogic(string $message, ?\Throwable $e = null): self
    {
        return new self($message, self::ERROR_CODE_LOGIC, $e);
    }
}
