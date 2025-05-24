<?php

declare(strict_types=1);

namespace Boson\Contracts\Http;

use Boson\Contracts\Http\Body\MutableBodyProviderInterface;
use Boson\Contracts\Http\Headers\MutableHeadersProviderInterface;
use Boson\Contracts\Http\Method\MutableMethodProviderInterface;
use Boson\Contracts\Http\Url\MutableUrlProviderInterface;

interface MutableRequestInterface extends
    MutableBodyProviderInterface,
    MutableHeadersProviderInterface,
    MutableMethodProviderInterface,
    MutableUrlProviderInterface,
    RequestInterface {}
