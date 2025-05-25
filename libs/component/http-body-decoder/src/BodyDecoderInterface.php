<?php

declare(strict_types=1);

namespace Boson\Component\Http\Body;

use Boson\Contracts\Http\RequestInterface;

/**
 * Responsible for decoding the request body.
 */
interface BodyDecoderInterface
{
    /**
     * Decodes request body.
     *
     * If decoding is not possible, then returns an empty array.
     *
     * @return array<non-empty-string, scalar|array<array-key, mixed>|null>
     */
    public function decode(RequestInterface $request): array;
}
