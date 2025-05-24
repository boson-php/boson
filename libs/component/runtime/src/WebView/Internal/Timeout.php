<?php

declare(strict_types=1);

namespace Boson\WebView\Internal;

use Psr\Clock\ClockInterface;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView
 */
final class Timeout
{
    /**
     * Returns instance created time.
     */
    private readonly float $now;

    /**
     * Returns time left after timeout creation.
     *
     * @noinspection PhpPropertyOnlyWrittenInspection
     */
    private float $left {
        get => $this->getCurrentMicrotime() - $this->now;
    }

    public function __construct(
        /**
         * Allows you to set time-dependent parameters for timer.
         *
         * If the value is not set (defined as {@see null}), the system
         * time will be used.
         */
        private ?ClockInterface $clock = null,
    ) {
        $this->now = $this->getCurrentMicrotime();
    }

    private function getCurrentMicrotime(): float
    {
        if ($this->clock !== null) {
            $now = $this->clock->now();
            $microtime = $now->getTimestamp();

            return $microtime + .000_001 * $now->getMicrosecond();
        }

        return \microtime(true);
    }

    public function isExceeded(float $seconds): bool
    {
        return $this->left >= $seconds;
    }
}
