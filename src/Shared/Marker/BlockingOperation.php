<?php

declare(strict_types=1);

namespace Boson\Shared\Marker;

/**
 * Marks methods that blocks current execution thread. This can slow down
 * the work and make it impossible to continue.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
final readonly class BlockingOperation {}
