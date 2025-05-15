<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Battery;

use Boson\Shared\Marker\ExpectsSecurityContext;
use Boson\WebView\Api\Battery\Exception\BatteryNotAvailableException;
use Boson\WebView\Api\Battery\Exception\BatteryNotReadyException;
use Boson\WebView\Api\Battery\Exception\InsecureBatteryContextException;
use Boson\WebView\Api\BatteryApiInterface;
use Boson\WebView\Api\Data\Exception\WebViewIsNotReadyException;
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
#[ExpectsSecurityContext]
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
        if (!$this->webview->security->isSecureContext) {
            throw InsecureBatteryContextException::becauseContextIsInsecure();
        }

        try {
            if ($this->webview->data->get('navigator.getBattery instanceof Function') !== true) {
                throw BatteryNotAvailableException::becauseBatteryNotAvailable();
            }
        } catch (WebViewIsNotReadyException $e) {
            throw BatteryNotReadyException::becauseBatteryNotReady($e);
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
