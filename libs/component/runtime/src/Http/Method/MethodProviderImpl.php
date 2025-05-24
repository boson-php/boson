<?php

declare(strict_types=1);

namespace Boson\Http\Method;

/**
 * @api
 *
 * @phpstan-require-implements MethodProviderInterface
 *
 * @phpstan-import-type MethodInputType from MutableMethodProviderInterface
 * @phpstan-import-type MethodOutputType from MethodProviderInterface
 *
 * @phpstan-ignore trait.unused
 */
trait MethodProviderImpl
{
    /**
     * @var MethodOutputType
     */
    public readonly string $method;

    /**
     * @param MethodInputType $method
     *
     * @return MethodOutputType
     */
    public static function castMethod(string|\Stringable $method): string
    {
        if (($methodScalarValue = (string) $method) === '') {
            return MutableMethodProviderInterface::DEFAULT_METHOD;
        }

        return \strtoupper($methodScalarValue);
    }
}
