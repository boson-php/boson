<?php

declare(strict_types=1);

namespace Boson\Contracts\Http\Method;

/**
 * @phpstan-import-type MethodOutputType from MethodProviderInterface
 *
 * @phpstan-type MethodInputType string|\Stringable
 * @phpstan-type MutableMethodOutputType MethodOutputType
 */
interface MutableMethodProviderInterface extends MethodProviderInterface
{
    /**
     * Contains default HTTP method definition value.
     *
     * @var MethodInputType
     */
    public const string|\Stringable DEFAULT_METHOD = 'GET';

    /**
     * Get behaviour similar to {@see MethodProviderInterface::$method}.
     *
     * @var MutableMethodOutputType
     */
    public string $method {
        get;
        /**
         * Also allows to set empty or non-normalized method name
         * string or object.
         *
         * @param MethodInputType $method
         */
        set(string|\Stringable $method);
    }
}
