<?php

declare(strict_types=1);

namespace Boson\Http\Uri\Factory;

use Boson\Http\Uri;
use Boson\Http\Uri\Authority;
use Boson\Http\Uri\AuthorityInterface;
use Boson\Http\Uri\Path\PathFactory;
use Boson\Http\Uri\Path\PathFactoryInterface;
use Boson\Http\Uri\Query\QueryFactory;
use Boson\Http\Uri\Query\QueryFactoryInterface;
use Boson\Http\Uri\Scheme\SchemeFactory;
use Boson\Http\Uri\Scheme\SchemeFactoryInterface;
use Boson\Http\Uri\SchemeInterface;
use Boson\Http\Uri\UserInfo;
use Boson\Http\Uri\UserInfoInterface;

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
final readonly class UriFactory implements UriFactoryInterface
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

    public function __construct(
        private SchemeFactoryInterface $schemes = new SchemeFactory(),
        private PathFactoryInterface $paths = new PathFactory(),
        private QueryFactoryInterface $queries = new QueryFactory(),
    ) {}

    public function createUriFromString(string $uri): Uri
    {
        $components = \parse_url($uri);

        if ($components === false) {
            $components = self::URI_COMPONENTS;
        } else {
            $components += self::URI_COMPONENTS;
        }

        return $this->createFromComponents($components);
    }

    /**
     * @param ComponentsArrayType $components
     */
    private function createSchemeFromComponents(array $components): ?SchemeInterface
    {
        if (($scheme = $components['scheme'] ?? '') === '') {
            return null;
        }

        return $this->schemes->createSchemeFromString($scheme);
    }

    /**
     * @param ComponentsArrayType $components
     */
    private function createAuthorityFromComponents(array $components): ?AuthorityInterface
    {
        if (!isset($components['host']) || $components['host'] === '') {
            return null;
        }

        return new Authority(
            host: $components['host'],
            port: $components['port'],
            userInfo: $this->createUserInfoFromComponents($components),
        );
    }

    /**
     * @param ComponentsArrayType $components
     */
    private function createUserInfoFromComponents(array $components): ?UserInfoInterface
    {
        if (!isset($components['user']) || $components['user'] === '') {
            return null;
        }

        return new UserInfo(
            user: $components['user'],
            password: $components['pass'] === '' ? null : $components['pass'],
        );
    }

    /**
     * @param ComponentsArrayType $components
     *
     * @return non-empty-string
     */
    private function createFragmentFromComponents(array $components): ?string
    {
        if (!isset($components['fragment']) || $components['fragment'] === '') {
            return null;
        }

        return \urldecode($components['fragment']);
    }

    /**
     * @param ComponentsArrayType $components
     */
    private function createFromComponents(array $components): Uri
    {
        return new Uri(
            path: $this->paths->createPathFromString($components['path']),
            query: $this->queries->createQueryFromString($components['query'] ?? ''),
            scheme: $this->createSchemeFromComponents($components),
            authority: $this->createAuthorityFromComponents($components),
            fragment: $this->createFragmentFromComponents($components),
        );
    }
}
