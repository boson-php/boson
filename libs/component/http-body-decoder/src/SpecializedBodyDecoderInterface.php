<?php

declare(strict_types=1);

namespace Boson\Component\Http\Body;

use Boson\Contracts\Http\RequestInterface;

/**
 * Responsible for optional decoding of the request body, depending
 * on external factors: For example, the presence of a driver
 * or the required header in the request.
 */
interface SpecializedBodyDecoderInterface extends BodyDecoderInterface
{
    /**
     * Returns {@see true} in case of body decoder supports decoding this body.
     */
    public function isDecodable(RequestInterface $request): bool;
}
