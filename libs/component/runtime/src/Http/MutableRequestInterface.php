<?php

declare(strict_types=1);

namespace Boson\Http;

use Boson\Http\Body\MutableBodyProviderInterface;
use Boson\Http\Headers\MutableHeadersProviderInterface;
use Boson\Http\Method\MutableMethodProviderInterface;
use Boson\Http\Url\MutableUrlProviderInterface;

interface MutableRequestInterface extends
    MutableBodyProviderInterface,
    MutableHeadersProviderInterface,
    MutableMethodProviderInterface,
    MutableUrlProviderInterface,
    RequestInterface {}
