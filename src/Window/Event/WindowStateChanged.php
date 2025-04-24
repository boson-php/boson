<?php

declare(strict_types=1);

namespace Boson\Window\Event;

use Boson\Shared\Marker\AsWindowEvent;
use Boson\Window\Window;
use Boson\Window\WindowState;

#[AsWindowEvent]
final class WindowStateChanged extends WindowEvent
{
    public function __construct(
        Window $subject,
        public readonly WindowState $state,
        public readonly WindowState $previous,
        ?int $time = null,
    ) {
        parent::__construct($subject, $time);
    }
}
