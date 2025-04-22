<?php

declare(strict_types=1);

namespace Boson\Http\Uri\Query;

use Boson\Http\Uri\QueryInterface;

interface QueryFactoryInterface
{
    /**
     * Creates a new {@see QueryInterface} instance from
     * passed {@see string} argument.
     */
    public function createQueryFromString(string $query): QueryInterface;
}
