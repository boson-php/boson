<?php

declare(strict_types=1);

namespace Boson\Bridge\Laravel\Http;

use Boson\Bridge\Symfony\Http\SymfonyHttpAdapter;
use Boson\Contracts\Http\RequestInterface;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * @template-extends SymfonyHttpAdapter<LaravelPatchedRequest, SymfonyResponse>
 */
readonly class LaravelHttpAdapter extends SymfonyHttpAdapter
{
    #[\Override]
    public function createRequest(RequestInterface $request): LaravelPatchedRequest
    {
        /** @var LaravelPatchedRequest */
        return LaravelPatchedRequest::createFromBase(
            request: parent::createRequest($request),
        );
    }
}
