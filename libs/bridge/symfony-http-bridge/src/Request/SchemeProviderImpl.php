<?php

declare(strict_types=1);

namespace Boson\Bridge\Symfony\Http\Request;

use Symfony\Component\HttpFoundation\Request;

/**
 * @phpstan-require-extends Request
 */
trait SchemeProviderImpl
{
    /**
     * @var non-empty-lowercase-string|null
     */
    private ?string $scheme = null;

    /**
     * @param array<array-key, mixed> $parameters
     * @param array<array-key, mixed> $cookies
     * @param array<array-key, mixed> $files
     * @param array<array-key, mixed> $server
     */
    #[\Override]
    public static function create(
        string $uri,
        string $method = 'GET',
        array $parameters = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        mixed $content = null,
    ): static {
        $instance = parent::create(
            uri: $uri,
            method: $method,
            parameters: $parameters,
            cookies: $cookies,
            files: $files,
            server: $server,
            content: $content,
        );

        $scheme = \parse_url($uri, PHP_URL_SCHEME);

        if (\is_string($scheme) && $scheme !== '') {
            $instance->scheme = \strtolower($scheme);
        }

        return $instance;
    }

    /**
     * @return non-empty-lowercase-string
     */
    public function getScheme(): string
    {
        /** @phpstan-ignore-next-line : parent's scheme is non-empty lowercase */
        return $this->scheme ??= parent::getScheme();
    }
}
