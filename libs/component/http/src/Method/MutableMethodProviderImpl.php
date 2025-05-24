<?php

declare(strict_types=1);

namespace Boson\Component\Http\Method;

use Boson\Component\Http\Request;
use Boson\Contracts\Http\Method\MutableMethodProviderInterface;

/**
 * @api
 *
 * @phpstan-require-implements MutableMethodProviderInterface
 *
 * @phpstan-import-type MethodInputType from MutableMethodProviderInterface
 * @phpstan-import-type MutableMethodOutputType from MutableMethodProviderInterface
 *
 * @phpstan-ignore trait.unused
 */
trait MutableMethodProviderImpl
{
    /**
     * @var MutableMethodOutputType
     */
    public string $method {
        get => $this->method;
        /**
         * @param MethodInputType $method
         */
        set(string|\Stringable $method) => self::castMutableMethod($method);
    }

    /**
     * @param MethodInputType $method
     *
     * @return MutableMethodOutputType
     */
    public static function castMutableMethod(string|\Stringable $method): string
    {
        return Request::castMethod($method);
    }
}
