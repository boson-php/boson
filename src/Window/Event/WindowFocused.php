<?php

declare(strict_types=1);

namespace Boson\Window\Event;

use Boson\Shared\Marker\AsWindowEvent;
use Boson\Window\Window;

#[AsWindowEvent]
final class WindowFocused extends WindowEvent
{
    public function __construct(
        Window $subject,
        public readonly bool $isFocused,
        ?int $time = null
    ) {
        parent::__construct($subject, $time);
    }
}
