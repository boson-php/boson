<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

use Boson\WebView\Api\BatteryApi\Exception\BatteryNotAvailableException;
use Boson\WebView\Api\BatteryApi\Exception\BatteryNotReadyException;

interface BatteryApiInterface
{
    /**
     * The level read-only property of interface indicates the current
     * battery charge level as a value between 0.0 and 1.0.
     *
     * A value of 0.0 means the battery is empty and the system is
     * about to be suspended. A value of 1.0 means the battery is full.
     */
    public float $level {
        /**
         * @throws BatteryNotReadyException in case the API is not ready
         * @throws BatteryNotAvailableException in case the API is not available
         */
        get;
    }

    /**
     * The charging read-only property of interface is a
     * {@see bool} value indicating whether or not the
     * device's battery is currently being charged.
     */
    public bool $isCharging {
        /**
         * @throws BatteryNotReadyException in case the API is not ready
         * @throws BatteryNotAvailableException in case the API is not available
         */
        get;
    }

    /**
     * The chargingTime read-only property of interface indicates the
     * amount of time, in seconds, that remain until the battery is
     * fully charged, or 0 if the battery is already fully charged.
     */
    public int $chargingTime {
        /**
         * @throws BatteryNotReadyException in case the API is not ready
         * @throws BatteryNotAvailableException in case the API is not available
         */
        get;
    }

    /**
     * The dischargingTime read-only property of interface indicates the
     * amount of time, in seconds, that remains until the battery is
     * fully discharged, or {@see null} if the battery is
     * currently charging rather than discharging.
     */
    public ?int $dischargingTime {
        /**
         * @throws BatteryNotReadyException in case the API is not ready
         * @throws BatteryNotAvailableException in case the API is not available
         */
        get;
    }
}
