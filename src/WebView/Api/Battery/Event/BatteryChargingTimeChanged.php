<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Battery\Event;

use Boson\Shared\Marker\AsWebViewEvent;
use Boson\WebView\WebView;

#[AsWebViewEvent]
final class BatteryChargingTimeChanged extends BatteryApiEvent
{
    public function __construct(
        WebView $subject,
        /**
         * @var int<0, max>
         */
        public readonly int $chargingTime,
        ?int $time = null,
    ) {
        parent::__construct($subject, $time);
    }
}
