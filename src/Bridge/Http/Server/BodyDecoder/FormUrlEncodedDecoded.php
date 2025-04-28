<?php

declare(strict_types=1);

namespace Boson\Bridge\Http\Server\BodyDecoder;

use Boson\Bridge\Http\Server\RequestTypeAwareDecoderInterface;
use Boson\Http\Headers\ContentTypeHeader;
use Boson\Http\RequestInterface;
use Boson\Http\Uri\Query\QueryFactory;
use Boson\Http\Uri\Query\QueryFactoryInterface;

/**
 * @template-implements RequestTypeAwareDecoderInterface<string|array<array-key, string>>
 */
final readonly class FormUrlEncodedDecoded implements RequestTypeAwareDecoderInterface
{
    public function __construct(
        private QueryFactoryInterface $queries = new QueryFactory(),
    ) {}

    public function decode(RequestInterface $request): array
    {
        return $this->queries->createQueryFromString($request->body)
            ->toArray();
    }

    public function isSupports(RequestInterface $request): bool
    {
        $contentType = $request->headers->first('content-type');

        if ($contentType instanceof ContentTypeHeader) {
            return $contentType->mimeType === 'application/x-www-form-urlencoded';
        }

        return false;
    }
}
