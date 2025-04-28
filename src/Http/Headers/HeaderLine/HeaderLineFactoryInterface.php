<?php

declare(strict_types=1);

namespace Boson\Http\Headers\HeaderLine;

interface HeaderLineFactoryInterface
{
    /**
     * @param non-empty-lowercase-string $name
     */
    public function createHeaderFromString(string $name, string|\Stringable $value): string|\Stringable;
}
