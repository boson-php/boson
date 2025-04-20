<?php

declare(strict_types=1);

namespace Boson\Shared\Marker;

/**
 * Marks any class as being a application event or intention.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class AsApplicationEvent {}
