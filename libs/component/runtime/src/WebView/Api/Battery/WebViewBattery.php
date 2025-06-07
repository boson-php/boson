<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Battery;

use Boson\Dispatcher\EventDispatcherInterface;
use Boson\Dispatcher\EventListenerInterface;
use Boson\Internal\Saucer\LibSaucer;
use Boson\Shared\Marker\ExpectsSecurityContext;
use Boson\WebView\Api\Battery\Event\BatteryChargingStateChanged;
use Boson\WebView\Api\Battery\Event\BatteryChargingTimeChanged;
use Boson\WebView\Api\Battery\Event\BatteryDischargingTimeChanged;
use Boson\WebView\Api\Battery\Event\BatteryLevelChanged;
use Boson\WebView\Api\Battery\Exception\BatteryNotAvailableException;
use Boson\WebView\Api\Battery\Exception\BatteryNotReadyException;
use Boson\WebView\Api\Battery\Exception\InsecureBatteryContextException;
use Boson\WebView\Api\BatteryApiInterface;
use Boson\WebView\Api\Data\Exception\WebViewIsNotReadyException;
use Boson\WebView\Api\WebViewExtension;
use Boson\WebView\WebView;

/**
 * @phpstan-type BatteryInfoType array{
 *     level: float|int<0, 1>,
 *     charging: bool,
 *     chargingTime: float|int<0, max>,
 *     dischargingTime: float|int<0, max>|null,
 * }
 */
#[ExpectsSecurityContext]
final class WebViewBattery extends WebViewExtension implements BatteryApiInterface
{
    public float $level {
        /** @phpstan-ignore-next-line : data can never be null */
        get => (float) $this->data['level'];
    }

    public bool $isCharging {
        /** @phpstan-ignore-next-line : data can never be null */
        get => $this->data['charging'];
    }

    public int $chargingTime {
        /** @phpstan-ignore-next-line : data can never be null */
        get => (int) $this->data['chargingTime'];
    }

    public ?int $dischargingTime {
        get => match (true) {
            /** @phpstan-ignore-next-line : data can never be null */
            $this->data['dischargingTime'] === null => null,
            default => (int) $this->data['dischargingTime'],
        };
    }

    /**
     * @var BatteryInfoType
     */
    private ?array $data = null {
        get => $this->data ??= $this->get();
    }

    public function __construct(
        LibSaucer $api,
        WebView $context,
        EventListenerInterface $listener,
        EventDispatcherInterface $dispatcher,
    ) {
        parent::__construct(
            api: $api,
            context: $context,
            listener: $listener,
            dispatcher: $dispatcher,
        );

        $this->registerDefaultFunctions();
        $this->registerDefaultClientEventListeners();
    }

    private function registerDefaultFunctions(): void
    {
        $this->context->bindings->bind('boson.battery.onLevelChange', $this->onLevelChange(...));
        $this->context->bindings->bind('boson.battery.onChargingChange', $this->onChargingChange(...));
        $this->context->bindings->bind('boson.battery.onChargingTimeChange', $this->onChargingTimeChange(...));
        $this->context->bindings->bind('boson.battery.onDischargingTimeChange', $this->onDischargingTimeChange(...));
    }

    /**
     * Registers default event listeners for webview events.
     */
    private function registerDefaultClientEventListeners(): void
    {
        $this->context->scripts->add(<<<'JS'
            document.addEventListener('levelchange', () => boson.battery.onLevelChange());
            document.addEventListener('chargingchange', () => boson.battery.onChargingChange());
            document.addEventListener('chargingtimechange', () => boson.battery.onChargingTimeChange());
            document.addEventListener('dischargingtimechange', () => boson.battery.onDischargingTimeChange());
            JS);
    }

    private function onLevelChange(): void
    {
        $this->flushState();

        $this->dispatch(new BatteryLevelChanged(
            subject: $this->context,
            level: $this->level,
        ));
    }

    private function onChargingChange(): void
    {
        $this->flushState();

        $this->dispatch(new BatteryChargingStateChanged(
            subject: $this->context,
            isCharging: $this->isCharging,
        ));
    }

    private function onChargingTimeChange(): void
    {
        $this->flushState();

        $this->dispatch(new BatteryChargingTimeChanged(
            subject: $this->context,
            chargingTime: $this->chargingTime,
        ));
    }

    private function onDischargingTimeChange(): void
    {
        $this->flushState();

        $this->dispatch(new BatteryDischargingTimeChanged(
            subject: $this->context,
            dischargingTime: $this->dischargingTime,
        ));
    }

    private function flushState(): void
    {
        $this->data = null;
    }

    /**
     * @return BatteryInfoType
     */
    private function get(): array
    {
        if (!$this->context->security->isSecureContext) {
            throw InsecureBatteryContextException::becauseContextIsInsecure();
        }

        try {
            if ($this->context->data->get('navigator.getBattery instanceof Function') !== true) {
                throw BatteryNotAvailableException::becauseBatteryNotAvailable();
            }
        } catch (WebViewIsNotReadyException $e) {
            throw BatteryNotReadyException::becauseBatteryNotReady($e);
        }

        /** @var BatteryInfoType */
        return $this->context->data->get('navigator.getBattery()
            .then((manager) => ({
                level: manager.level,
                charging: manager.charging,
                chargingTime: manager.chargingTime,
                dischargingTime: manager.dischargingTime,
            })) || {}');
    }
}
