<?php

declare(strict_types=1);

namespace Boson\Http\StatusCode;

use Boson\Http\StatusCode\Category\HttpCategory;
use Boson\Http\StatusCode\Category\StandardCategory;
use Boson\Shared\ValueObject\UnitEnumLikeImpl;

/**
 * @internal this constant cannot be autoloaded, please use
 *           {@see Category::Informational} instead
 */
const CATEGORY_INFORMATIONAL = new HttpCategory('Informational', 100);

/**
 * @internal this constant cannot be autoloaded, please use
 *           {@see Category::Successful} instead
 */
const CATEGORY_SUCCESSFUL = new HttpCategory('Successful', 200);

/**
 * @internal this constant cannot be autoloaded, please use
 *           {@see Category::Redirection} instead
 */
const CATEGORY_REDIRECTION = new HttpCategory('Redirection', 300);

/**
 * @internal this constant cannot be autoloaded, please use
 *           {@see Category::ClientError} instead
 */
const CATEGORY_CLIENT_ERROR = new HttpCategory('Client Error', 400);

/**
 * @internal this constant cannot be autoloaded, please use
 *           {@see Category::ServerError} instead
 */
const CATEGORY_SERVER_ERROR = new HttpCategory('Server Error', 500);

final readonly class Category extends StandardCategory
{
    /** @use UnitEnumLikeImpl<CategoryInterface> */
    use UnitEnumLikeImpl;

    /**
     * Informational HTTP responses
     */
    final public const HttpCategory Informational = CATEGORY_INFORMATIONAL;

    /**
     * Successful HTTP responses
     */
    final public const HttpCategory Successful = CATEGORY_SUCCESSFUL;

    /**
     * Redirection HTTP messages
     */
    final public const HttpCategory Redirection = CATEGORY_REDIRECTION;

    /**
     * Client HTTP error responses
     */
    final public const HttpCategory ClientError = CATEGORY_CLIENT_ERROR;

    /**
     * Server HTTP error responses
     */
    final public const HttpCategory ServerError = CATEGORY_SERVER_ERROR;
}
