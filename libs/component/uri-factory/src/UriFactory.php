<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Factory;

use Boson\Component\Uri\Component\Authority;
use Boson\Component\Uri\Component\UserInfo;
use Boson\Component\Uri\Factory\Component\UriPathFactory;
use Boson\Component\Uri\Factory\Component\UriQueryFactory;
use Boson\Component\Uri\Factory\Component\UriSchemeFactory;
use Boson\Component\Uri\Factory\Exception\InvalidUriException;
use Boson\Component\Uri\Uri;
use Boson\Contracts\Uri\Component\SchemeInterface;
use Boson\Contracts\Uri\Factory\Component\UriPathFactoryInterface;
use Boson\Contracts\Uri\Factory\Component\UriQueryFactoryInterface;
use Boson\Contracts\Uri\Factory\Component\UriSchemeFactoryInterface;
use Boson\Contracts\Uri\Factory\Exception\InvalidUriComponentExceptionInterface;
use Boson\Contracts\Uri\Factory\UriFactoryInterface;
use Boson\Contracts\Uri\UriInterface;

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
        private UriSchemeFactoryInterface $schemes = new UriSchemeFactory(),
        private UriPathFactoryInterface $paths = new UriPathFactory(),
        private UriQueryFactoryInterface $queries = new UriQueryFactory(),
    ) {}

    public function createUriFromString(\Stringable|string $uri): UriInterface
    {
        if ($uri instanceof UriInterface) {
            return clone $uri;
        }

        if ($uri instanceof \Stringable) {
            try {
                $scalar = (string) $uri;
                /** @phpstan-ignore-next-line : PHPStan false-positive, this is not dead catch */
            } catch (\Throwable $e) {
                throw InvalidUriException::becauseStringCastingErrorOccurs($uri, $e);
            }

            $uri = $scalar;
        }

        $components = \parse_url($uri);

        if ($components === false) {
            $components = self::URI_COMPONENTS;
        } else {
            $components += self::URI_COMPONENTS;
        }

        try {
            return $this->createFromComponents($components);
        } catch (InvalidUriComponentExceptionInterface $e) {
            throw InvalidUriException::becauseUriComponentIsInvalid($e);
        }
    }

    /**
     * @param ComponentsArrayType $components
     *
     * @throws InvalidUriComponentExceptionInterface
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
    private function createAuthorityFromComponents(array $components): ?Authority
    {
        if (!isset($components['host']) || $components['host'] === '') {
            return null;
        }

        return new Authority(
            host: $components['host'],
            port: $components['port'],
            info: $this->createUserInfoFromComponents($components),
        );
    }

    /**
     * @param ComponentsArrayType $components
     */
    private function createUserInfoFromComponents(array $components): ?UserInfo
    {
        if (!isset($components['user'])) {
            return null;
        }

        return new UserInfo(
            user: $components['user'],
            password: $components['pass'],
        );
    }

    /**
     * @param ComponentsArrayType $components
     *
     * @return non-empty-string|null
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
     *
     * @throws InvalidUriComponentExceptionInterface
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
