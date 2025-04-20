<?php

declare(strict_types=1);

namespace Boson\WebView\Url;

use Boson\WebView\Url;

/**
 * @phpstan-type ComponentsArrayType array{
 *     scheme: string|null,
 *     user: string|null,
 *     pass: string|null,
 *     host: string|null,
 *     port: int<0, 65535>|null,
 *     path: string,
 *     query: string|null,
 *     fragment: string|null
 * }
 */
final readonly class NativeUrlParser implements UrlParserInterface
{
    /**
     * Default URI component values.
     *
     * @var ComponentsArrayType
     */
    private const array URI_COMPONENTS = [
        'scheme' => null,
        'user' => null,
        'pass' => null,
        'host' => null,
        'port' => null,
        'path' => '',
        'query' => null,
        'fragment' => null,
    ];

    public function parse(string $url): Url
    {
        $components = \parse_url($url);

        if ($components === false) {
            $components = self::URI_COMPONENTS;
        } else {
            $components += self::URI_COMPONENTS;
        }

        return self::createFromComponents($url, $components);
    }

    private static function formatPath(string $path): string
    {
        return match (true) {
            \str_starts_with($path, '//') => '/' . \ltrim($path, '/'),
            default => $path,
        };
    }

    /**
     * @param ComponentsArrayType $components
     */
    private static function createFromComponents(string $uri, array $components): Url
    {
        return new Url(
            scheme: $components['scheme'],
            user: $components['user'],
            password: $components['pass'],
            host: $components['host'],
            port: $components['port'],
            path: self::formatPath($components['path']),
            query: $components['query'],
            fragment: $components['fragment'],
            value: $uri,
        );
    }
}
