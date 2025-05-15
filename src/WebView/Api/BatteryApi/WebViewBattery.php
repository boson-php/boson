<?php

declare(strict_types=1);

namespace Boson\WebView\Api\BatteryApi;

use Boson\WebView\Api\BatteryApi\Exception\BatteryNotAvailableException;
use Boson\WebView\Api\BatteryApi\Exception\BatteryNotReadyException;
use Boson\WebView\Api\BatteryApi\Exception\InsecureBatteryContextException;
use Boson\WebView\Api\BatteryApiInterface;
use Boson\WebView\Api\WebViewApi;
use Boson\WebView\WebViewState;

/**
 * @phpstan-type BatteryInfoType array{
 *     level: float|int<0, 1>,
 *     charging: bool,
 *     chargingTime: float|int<0, max>,
 *     dischargingTime: float|int<0, max>|null,
 * }
 */
final class WebViewBattery extends WebViewApi implements BatteryApiInterface
{
    public float $level {
        get => (float) $this->data['level'];
    }

    public bool $isCharging {
        get => $this->data['charging'];
    }

    public int $chargingTime {
        get => (int) $this->data['chargingTime'];
    }

    public ?int $dischargingTime {
        get => match (true) {
            $this->data['dischargingTime'] === null => null,
            default => (int) $this->data['dischargingTime'],
        };
    }

    /**
     * @var BatteryInfoType
     */
    private array $data {
        get => $this->data ??= $this->get();
    }

    /**
     * @return BatteryInfoType
     */
    private function get(): array
    {
        if ($this->webview->state !== WebViewState::Ready) {
            throw BatteryNotReadyException::becauseBatteryNotReady($this->webview->state);
        }

        if (!$this->webview->security->isSecureContext) {
            throw InsecureBatteryContextException::becauseContextIsInsecure();
        }

        if ($this->webview->data->get('navigator.getBattery instanceof Function') !== true) {
            throw BatteryNotAvailableException::becauseBatteryNotAvailable();
        }

        /** @var BatteryInfoType */
        return $this->webview->data->get('navigator.getBattery()
            .then((manager) => ({
                level: manager.level,
                charging: manager.charging,
                chargingTime: manager.chargingTime,
                dischargingTime: manager.dischargingTime,
            })) || {}');
    }
}
