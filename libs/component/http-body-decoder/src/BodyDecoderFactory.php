<?php

declare(strict_types=1);

namespace Boson\Component\Http\Body;

use Boson\Contracts\Http\RequestInterface;

/**
 * Factory, which selects the most suitable decoder from
 * the list of registered ones.
 */
final readonly class BodyDecoderFactory implements BodyDecoderInterface
{
    /**
     * @var list<SpecializedBodyDecoderInterface>
     */
    private array $decoders;

    /**
     * @param iterable<mixed, SpecializedBodyDecoderInterface> $decoders
     */
    public function __construct(iterable $decoders)
    {
        $this->decoders = \iterator_to_array($decoders, false);
    }

    public function decode(RequestInterface $request): array
    {
        foreach ($this->decoders as $decoder) {
            if ($decoder->isDecodable($request)) {
                try {
                    return $decoder->decode($request);
                } catch (\Throwable) {
                    // Decoder should not throw an exception while decoding
                }
            }
        }

        return [];
    }
}
