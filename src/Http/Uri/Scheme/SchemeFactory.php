<?php

declare(strict_types=1);

namespace Boson\Http\Uri\Scheme;

use Boson\Http\Uri\Scheme;
use Boson\Http\Uri\SchemeInterface;

final readonly class SchemeFactory implements SchemeFactoryInterface
{
    public function createSchemeFromString(string $scheme): SchemeInterface
    {
        return Scheme::tryFrom(\strtolower($scheme))
            ?? $this->createUserDefinedScheme($scheme);
    }

    /**
     * @param non-empty-string $scheme
     */
    private function createUserDefinedScheme(string $scheme): Scheme
    {
        return new Scheme($scheme);
    }
}
