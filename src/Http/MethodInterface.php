<?php

declare(strict_types=1);

namespace Boson\Http;

use Boson\WebView\Http\non;

/**
 * HTTP defines a set of request methods to indicate the purpose of the
 * request and what is expected if the request is successful.
 *
 * Although they can also be nouns, these request methods are sometimes
 * referred to as HTTP verbs. Each request method has its own semantics,
 * but some characteristics are shared across multiple methods,
 * specifically request methods can be safe, idempotent, or cacheable.
 */
interface MethodInterface extends \Stringable
{
    /**
     * The name of the HTTP method (e.g., `GET`, `POST`, `PUT`, `DELETE`).
     * This is a human-readable string representation of the method.
     *
     * Method name MUST be uppercased.
     *
     * @var non-empty-string
     *
     * @phpstan-var non-empty-uppercase-string
     */
    public string $name { get; }

    /**
     * An HTTP method is idempotent if the intended effect on the server of
     * making a single request is the same as the effect of making several
     * identical requests.
     */
    public bool $isIdempotent { get; }

    /**
     * An HTTP method is safe if it doesn't alter the state of the server.
     * In other words, a method is safe if it leads to a read-only operation.
     */
    public bool $isSafe { get; }
}
