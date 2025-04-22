<?php

declare(strict_types=1);

namespace Boson\Http\Uri\Path;

use Boson\Http\Uri\PathInterface;

interface PathFactoryInterface
{
    /**
     * Returns {@see PathInterface} from {@see string} path representation.
     */
    public function createPathFromString(string $path): PathInterface;
}
