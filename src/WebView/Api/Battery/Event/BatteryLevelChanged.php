<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Battery\Event;

use Boson\Shared\Marker\AsWebViewEvent;
use Boson\WebView\WebView;

#[AsWebViewEvent]
final class BatteryLevelChanged extends BatteryApiEvent
{
    public function __construct(
        WebView $subject,
        public readonly float $level,
        ?int $time = null,
    ) {
        parent::__construct($subject, $time);
    }
}
