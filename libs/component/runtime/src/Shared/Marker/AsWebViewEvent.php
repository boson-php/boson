<?php

declare(strict_types=1);

namespace Boson\Shared\Marker;

/**
 * Marks any class as being a webview event.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
readonly class AsWebViewEvent {}
