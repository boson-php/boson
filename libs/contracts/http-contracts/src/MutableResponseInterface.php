<?php

declare(strict_types=1);

namespace Boson\Contracts\Http;

use Boson\Contracts\Http\Body\MutableBodyProviderInterface;
use Boson\Contracts\Http\Headers\MutableHeadersProviderInterface;
use Boson\Contracts\Http\StatusCode\MutableStatusCodeProviderInterface;

interface MutableResponseInterface extends
    MutableBodyProviderInterface,
    MutableHeadersProviderInterface,
    MutableStatusCodeProviderInterface,
    ResponseInterface {}
