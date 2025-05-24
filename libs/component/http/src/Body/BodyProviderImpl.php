<?php

declare(strict_types=1);

namespace Boson\Component\Http\Body;

use Boson\Contracts\Http\Body\BodyProviderInterface;
use Boson\Contracts\Http\Body\MutableBodyProviderInterface;

/**
 * @api
 *
 * @phpstan-require-implements BodyProviderInterface
 *
 * @phpstan-import-type BodyInputType from MutableBodyProviderInterface
 * @phpstan-import-type BodyOutputType from BodyProviderInterface
 *
 * @phpstan-ignore trait.unused
 */
trait BodyProviderImpl
{
    /**
     * @var BodyOutputType
     */
    public readonly string $body;

    /**
     * @param BodyInputType $body
     *
     * @return BodyOutputType
     */
    public static function castBody(string|\Stringable $body): string
    {
        return (string) $body;
    }
}
