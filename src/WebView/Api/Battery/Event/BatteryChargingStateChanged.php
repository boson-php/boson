<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Battery\Event;

use Boson\Shared\Marker\AsWebViewEvent;
use Boson\WebView\WebView;

#[AsWebViewEvent]
final class BatteryChargingStateChanged extends BatteryApiEvent
{
    public function __construct(
        WebView $subject,
        public readonly bool $isCharging,
        ?int $time = null,
    ) {
        parent::__construct($subject, $time);
    }
}
