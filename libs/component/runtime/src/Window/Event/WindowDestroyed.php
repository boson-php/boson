<?php

declare(strict_types=1);

namespace Boson\Window\Event;

use Boson\Shared\Marker\AsWindowEvent;

#[AsWindowEvent]
final class WindowDestroyed extends WindowEvent {}
