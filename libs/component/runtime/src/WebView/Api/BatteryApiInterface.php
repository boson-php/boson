<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

use Boson\Shared\Marker\ExpectsSecurityContext;
use Boson\WebView\Api\Battery\BatteryInfoProviderInterface;

#[ExpectsSecurityContext]
interface BatteryApiInterface extends
    BatteryInfoProviderInterface {}
