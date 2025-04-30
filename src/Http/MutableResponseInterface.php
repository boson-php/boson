<?php

declare(strict_types=1);

namespace Boson\Http;

use Boson\Http\Body\MutableBodyProviderInterface;
use Boson\Http\Headers\MutableHeadersProviderInterface;
use Boson\Http\StatusCode\MutableStatusCodeProviderInterface;

interface MutableResponseInterface extends
    MutableBodyProviderInterface,
    MutableHeadersProviderInterface,
    MutableStatusCodeProviderInterface,
    ResponseInterface {}
