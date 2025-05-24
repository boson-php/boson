<?php

declare(strict_types=1);

namespace Boson\Contracts\Http;

use Boson\Contracts\Http\Body\BodyProviderInterface;
use Boson\Contracts\Http\Headers\HeadersProviderInterface;
use Boson\Contracts\Http\Method\MethodProviderInterface;
use Boson\Contracts\Http\Url\UrlProviderInterface;

interface RequestInterface extends
    BodyProviderInterface,
    HeadersProviderInterface,
    MethodProviderInterface,
    UrlProviderInterface {}
