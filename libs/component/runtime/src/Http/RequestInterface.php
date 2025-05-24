<?php

declare(strict_types=1);

namespace Boson\Http;

use Boson\Http\Body\BodyProviderInterface;
use Boson\Http\Headers\HeadersProviderInterface;
use Boson\Http\Method\MethodProviderInterface;
use Boson\Http\Url\UrlProviderInterface;

interface RequestInterface extends
    BodyProviderInterface,
    HeadersProviderInterface,
    MethodProviderInterface,
    UrlProviderInterface {}
