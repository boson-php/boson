<?php

declare(strict_types=1);

namespace Boson\Contracts\Uri\Factory;

use Boson\Contracts\Uri\Factory\Exception\InvalidUriExceptionInterface;
use Boson\Contracts\Uri\UriInterface;

/**
 * Provides an interface to create URI instances.
 */
interface UriFactoryInterface
{
    /**
     * Parse given URL/URI {@see string} or objects, that instance of
     * {@see \Stringable} interface to an {@see UriInterface} instance.
     *
     * This means that when parsing, you have access to the functionality
     * of transforming any object that implements URI. For example,
     * [PSR-7 URI](https://www.php-fig.org/psr/psr-7/#35-psrhttpmessageuriinterface).
     *
     * ```
     * $simpleUri = $factory->createUriFromString('about:blank');
     *
     * $commonUri = $factory->createUriFromString('http://example.com');
     *
     * $fromPsr7 = $factory->createUriFromString($psr7Request->getUri());
     *
     * $fromLeagueUri = $factory->createUriFromString($leagueUri);
     * ```
     *
     * @throws InvalidUriExceptionInterface in case of invalid URI is passed
     */
    public function createUriFromString(\Stringable|string $uri): UriInterface;
}
