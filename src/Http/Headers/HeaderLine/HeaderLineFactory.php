<?php

declare(strict_types=1);

namespace Boson\Http\Headers\HeaderLine;

use Boson\Http\Headers\ContentDispositionHeader;
use Boson\Http\Headers\ContentTypeHeader;

final class HeaderLineFactory implements HeaderLineFactoryInterface
{
    public function createHeaderFromString(string $name, string|\Stringable $value): string|\Stringable
    {
        return match ($name) {
            'content-type' => new ContentTypeHeader((string) $value),
            'content-disposition' => new ContentDispositionHeader((string) $value),
            default => $value,
        };
    }
}
