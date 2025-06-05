<?php

declare(strict_types=1);

namespace Boson\Bridge\Laravel\Http;

use Boson\Bridge\Symfony\Http\Request\SchemeProviderImpl;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

final class LaravelPatchedRequest extends Request
{
    use SchemeProviderImpl;

    #[\Override]
    public static function createFromBase(SymfonyRequest $request): static
    {
        $instance = parent::createFromBase($request);

        if (($scheme = $request->getScheme()) !== '') {
            $instance->scheme = \strtolower($scheme);
        }

        return $instance;
    }
}
