<?php

declare(strict_types=1);

namespace Boson\Bridge\Symfony\Http;

use Boson\Bridge\Symfony\Http\Request\SchemeProviderImpl;
use Symfony\Component\HttpFoundation\Request;

final class SymfonyPatchedRequest extends Request
{
    use SchemeProviderImpl;
}
