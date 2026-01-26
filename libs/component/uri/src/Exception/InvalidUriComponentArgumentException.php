<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Exception;

use Boson\Contracts\Uri\Exception\InvalidArgumentExceptionInterface;

class InvalidUriComponentArgumentException extends UriComponentException implements
    InvalidArgumentExceptionInterface
{
    final public const int ERROR_CODE_STRINGABLE = 0x01;
    final public const int ERROR_CODE_IS_EMPTY = 0x02;

    public static function becauseStringableErrorOccurs(\Throwable $e): self
    {
        $message = \sprintf('An error occurred while casting the URI component to string: %s', $e->getMessage());

        return new self($message, self::ERROR_CODE_STRINGABLE, $e);
    }

    public static function becauseComponentIsEmpty(string $component, ?\Throwable $e = null): self
    {
        $message = \sprintf('The "%s" of URI component cannot be empty', $component);

        return new self($message, self::ERROR_CODE_IS_EMPTY, $e);
    }
}
