<?php

declare(strict_types=1);

namespace Boson\Event;

use Boson\Shared\Marker\AsApplicationEvent;

#[AsApplicationEvent]
final class ApplicationStopped extends ApplicationEvent {}
