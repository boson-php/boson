<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Schemes\Request;

use Boson\Component\Saucer\SaucerInterface;
use Boson\Contracts\Http\RequestInterface;
use FFI\CData;

/**
 * Creates Boson request instance from unmanaged Saucer request pointer
 */
final readonly class SaucerRequestFactory
{
    public function __construct(
        private SaucerInterface $saucer,
    ) {}

    public function createFromSaucerRequest(CData $request): RequestInterface
    {
        return new SaucerRequest($this->saucer, $request);
    }
}
