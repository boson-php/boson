<?php

declare(strict_types=1);

namespace Boson\Http;

use Boson\Http\Body\BodyProviderInterface;
use Boson\Http\Headers\HeadersProviderInterface;
use Boson\Http\StatusCode\StatusCodeProviderInterface;

interface ResponseInterface extends
    BodyProviderInterface,
    HeadersProviderInterface,
    StatusCodeProviderInterface {}
