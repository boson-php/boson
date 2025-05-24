<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Battery\Event;

use Boson\Shared\Marker\AsWebViewEvent;
use Boson\WebView\WebView;

#[AsWebViewEvent]
final class BatteryDischargingTimeChanged extends BatteryApiEvent
{
    public function __construct(
        WebView $subject,
        /**
         * @var int<0, max>|null
         */
        public readonly ?int $dischargingTime,
        ?int $time = null,
    ) {
        parent::__construct($subject, $time);
    }
}
