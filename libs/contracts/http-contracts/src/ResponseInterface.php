<?php

declare(strict_types=1);

namespace Boson\Contracts\Http;

use Boson\Contracts\Http\Body\BodyProviderInterface;
use Boson\Contracts\Http\Headers\HeadersProviderInterface;
use Boson\Contracts\Http\StatusCode\StatusCodeProviderInterface;

interface ResponseInterface extends
    BodyProviderInterface,
    HeadersProviderInterface,
    StatusCodeProviderInterface {}
