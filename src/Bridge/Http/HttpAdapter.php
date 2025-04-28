<?php

declare(strict_types=1);

namespace Boson\Bridge\Http;

use Boson\Bridge\Http\Server\BodyDecoder;
use Boson\Bridge\Http\Server\BodyDecoder\FormUrlEncodedDecoded;
use Boson\Bridge\Http\Server\BodyDecoder\JsonBodyDecoder;
use Boson\Bridge\Http\Server\BodyDecoder\MultipartFormDataDecoder;
use Boson\Bridge\Http\Server\GlobalsDecoderInterface;
use Boson\Bridge\Http\Server\ServerGlobalsDecoder;

/**
 * @template-covariant TRequest of object
 * @template TResponse of object
 *
 * @template-implements RequestAdapterInterface<TRequest>
 * @template-implements ResponseAdapterInterface<TResponse>
 */
abstract readonly class HttpAdapter implements
    RequestAdapterInterface,
    ResponseAdapterInterface
{
    /**
     * @var GlobalsDecoderInterface<scalar>
     */
    protected GlobalsDecoderInterface $server;

    /**
     * @var GlobalsDecoderInterface<mixed>
     */
    protected GlobalsDecoderInterface $post;

    /**
     * @param GlobalsDecoderInterface<scalar>|null $server
     * @param GlobalsDecoderInterface<mixed>|null $post
     */
    public function __construct(
        ?GlobalsDecoderInterface $server = null,
        ?GlobalsDecoderInterface $post = null,
    ) {
        $this->server = $server ?? $this->createServerGlobalsDecoder();
        $this->post = $post ?? $this->createPostGlobalsDecoder();
    }

    /**
     * @return GlobalsDecoderInterface<scalar>
     */
    protected function createServerGlobalsDecoder(): GlobalsDecoderInterface
    {
        return new ServerGlobalsDecoder();
    }

    /**
     * @return GlobalsDecoderInterface<mixed>
     */
    protected function createPostGlobalsDecoder(): GlobalsDecoderInterface
    {
        return new BodyDecoder([
            new FormUrlEncodedDecoded(),
            new MultipartFormDataDecoder(),
        ]);
    }
}
