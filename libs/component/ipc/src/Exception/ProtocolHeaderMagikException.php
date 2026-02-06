<?php

declare(strict_types=1);

namespace Boson\Component\Ipc\Exception;

class ProtocolHeaderMagikException extends ProtocolException
{
    public static function becauseInvalidMagik(?\Throwable $prev = null): self
    {
        $message = 'There is incorrect data in memory; perhaps an attempt was made to connect to another memory area';

        return new self($message, 0, $prev);
    }
}
