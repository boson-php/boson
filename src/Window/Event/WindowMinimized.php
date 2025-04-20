<?php

declare(strict_types=1);

namespace Boson\Window\Event;

use Boson\Shared\Marker\AsWindowEvent;
use Boson\Window\Window;

#[AsWindowEvent]
final class WindowMinimized extends WindowEvent
{
    public function __construct(
        Window $subject,
        public readonly bool $isMinimized,
        ?int $time = null,
    ) {
        parent::__construct($subject, $time);
    }
}
