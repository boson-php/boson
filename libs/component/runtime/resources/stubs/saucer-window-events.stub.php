<?php

namespace Boson\Internal\Window;

use FFI\CData;

/**
 * @internal this is an INTERNAL STRUCT for PHPStan only, please do not use it in your code
 * @psalm-internal Boson\Internal\Window
 *
 * @seal-properties
 * @seal-methods
 */
final class CSaucerWindowEventsStruct extends CData
{
    /**
     * @var \Closure(CData, bool): void
     */
    public \Closure $onDecorated;

    /**
     * @var \Closure(CData, bool): void
     */
    public \Closure $onMaximize;

    /**
     * @var \Closure(CData, bool): void
     */
    public \Closure $onMinimize;

    /**
     * @var \Closure(CData): SaucerPolicy::SAUCER_POLICY_*
     */
    public \Closure $onClosing;

    /**
     * @var \Closure(CData): void
     */
    public \Closure $onClosed;

    /**
     * @var \Closure(CData, int<0, 2147483647>, int<0, 2147483647>): void
     */
    public \Closure $onResize;

    /**
     * @var \Closure(CData, bool): void
     */
    public \Closure $onFocus;
}
