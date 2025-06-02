<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Assembly\Edition;

use Boson\Component\Compiler\Assembly\Edition;
use Boson\Component\Compiler\Assembly\EditionInterface;

/**
 * @internal this is an internal library class, please do not use it in your code.
 * @psalm-internal Boson\Component\Compiler\Assembly
 */
final readonly class BuiltinEdition implements EditionInterface
{
    use EditionImpl;

    public static function tryFrom(string $name): ?BuiltinEdition
    {
        return [
            'minimal' => Edition::Minimal,
            'standard' => Edition::Standard,
        ][\strtolower($name)] ?? null;
    }
}
