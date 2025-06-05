<?php

declare(strict_types=1);

namespace Boson\Component\Http\Body;

use Boson\Contracts\Http\RequestInterface;

/**
 * An "application/x-www-form-urlencoded" body decoder using PHP behaviour.
 */
final readonly class FormUrlEncodedDecoder implements SpecializedBodyDecoderInterface
{
    /**
     * @var non-empty-lowercase-string
     */
    private const string EXPECTED_CONTENT_TYPE = 'application/x-www-form-urlencoded';

    public function decode(RequestInterface $request): array
    {
        \parse_str($request->body, $body);

        /** @phpstan-ignore-next-line : Known issue */
        return $body;
    }

    public function isDecodable(RequestInterface $request): bool
    {
        return $request->headers->first('content-type') === self::EXPECTED_CONTENT_TYPE;
    }
}
