<?php

declare(strict_types=1);

namespace Boson\Http\Uri\Scheme;

use Boson\Http\Uri\SchemeInterface;

interface SchemeFactoryInterface
{
    /**
     * Returns {@see SchemeInterface} instance from passed
     * non empty {@see string} argument.
     *
     * @param non-empty-string $scheme
     */
    public function createSchemeFromString(string $scheme): SchemeInterface;
}
