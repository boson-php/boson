<?php

declare(strict_types=1);

namespace Boson\Shared\Marker;

/**
 * Marks API that requires security context.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/Security/Secure_Contexts
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_CLASS)]
final readonly class ExpectsSecurityContext {}
