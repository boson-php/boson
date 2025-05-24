<?php

declare(strict_types=1);

namespace Boson\Http\Body;

/**
 * @api
 *
 * @phpstan-require-implements MutableBodyProviderInterface
 *
 * @phpstan-import-type BodyInputType from MutableBodyProviderInterface
 * @phpstan-import-type MutableBodyOutputType from MutableBodyProviderInterface
 *
 * @phpstan-ignore trait.unused
 */
trait MutableBodyProviderImpl
{
    /**
     * @var MutableBodyOutputType
     */
    public string $body {
        get => $this->body;
        /**
         * @param BodyInputType $body
         */
        set(string|\Stringable $body) => self::castMutableBody($body);
    }

    /**
     * @param BodyInputType $body
     *
     * @return MutableBodyOutputType
     */
    public static function castMutableBody(string|\Stringable $body): string
    {
        return (string) $body;
    }
}
