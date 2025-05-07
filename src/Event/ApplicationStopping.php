<?php

declare(strict_types=1);

namespace Boson\Event;

use Boson\Shared\Marker\AsApplicationIntention;

#[AsApplicationIntention]
final class ApplicationStopping extends ApplicationIntention {}
