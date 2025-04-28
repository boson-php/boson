<?php

declare(strict_types=1);

namespace Boson\Bridge\Http\Server;

use Boson\Http\RequestInterface;

/**
 * Extract and parse body (by analogy with PHP `$_POST` global variable).
 *
 * @template-implements GlobalsDecoderInterface<mixed>
 */
final readonly class BodyDecoder implements GlobalsDecoderInterface
{
    /**
     * @var list<RequestTypeAwareDecoderInterface<mixed>>
     */
    private array $converters;

    /**
     * @param iterable<mixed, RequestTypeAwareDecoderInterface<mixed>> $converters
     */
    public function __construct(iterable $converters)
    {
        $this->converters = \iterator_to_array($converters, false);
    }

    public function decode(RequestInterface $request): array
    {
        foreach ($this->converters as $converter) {
            if ($converter->isSupports($request)) {
                return $converter->decode($request);
            }
        }

        return [];
    }
}
