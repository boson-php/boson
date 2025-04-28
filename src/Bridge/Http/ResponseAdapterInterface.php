<?php

declare(strict_types=1);

namespace Boson\Bridge\Http;

use Boson\Http\ResponseInterface;

/**
 * @template TResponse of object
 */
interface ResponseAdapterInterface
{
    /**
     * Creates new Boson response instance from internal
     * (framework-aware) response argument.
     *
     * @param TResponse $response
     */
    public function createResponse(object $response): ResponseInterface;
}
