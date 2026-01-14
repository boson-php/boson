<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Schemes\Event;

if (!\class_exists(SchemeRequestReceived::class)) {
    \class_alias(SchemeRequestReceive::class, SchemeRequestReceived::class);
}
