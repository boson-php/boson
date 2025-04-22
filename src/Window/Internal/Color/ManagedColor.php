<?php

declare(strict_types=1);

namespace Boson\Window\Internal\Color;

use Boson\Internal\Saucer\LibSaucer;
use Boson\Window\Color\ColorStringableImpl;
use Boson\Window\MutableColorInterface;
use FFI\CData;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Window
 */
final class ManagedColor implements MutableColorInterface
{
    use ColorStringableImpl;

    /**
     * @var int<0, 255>
     */
    public int $red {
        /** @phpstan-ignore-next-line : CData contain uint8_t type */
        get => $this->unmanagedRedValue->cdata;
        set { $this->update(red: $value); }
    }

    /**
     * @var object{cdata:int<0, 255>}&CData
     */
    private readonly CData $unmanagedRedValue;

    /**
     * @var int<0, 255>
     */
    public int $green {
        /** @phpstan-ignore-next-line : CData contain uint8_t type */
        get => $this->unmanagedGreenValue->cdata;
        set { $this->update(green: $value); }
    }

    /**
     * @var object{cdata:int<0, 255>}&CData
     */
    private readonly CData $unmanagedGreenValue;

    /**
     * @var int<0, 255>
     */
    public int $blue {
        /** @phpstan-ignore-next-line : CData contain uint8_t type */
        get => $this->unmanagedBlueValue->cdata;
        set { $this->update(blue: $value); }
    }

    /**
     * @var object{cdata:int<0, 255>}&CData
     */
    private readonly CData $unmanagedBlueValue;

    /**
     * @var int<0, 255>
     */
    public int $alpha {
        /** @phpstan-ignore-next-line : CData contain uint8_t type */
        get => $this->unmanagedAlphaValue->cdata;
        set { $this->update(alpha: $value); }
    }

    /**
     * @var object{cdata:int<0, 255>}&CData
     */
    private readonly CData $unmanagedAlphaValue;

    public function __construct(
        private readonly LibSaucer $api,
        private readonly CData $handle,
    ) {
        $this->unmanagedRedValue = $this->api->new('uint8_t');
        $this->unmanagedGreenValue = $this->api->new('uint8_t');
        $this->unmanagedBlueValue = $this->api->new('uint8_t');
        $this->unmanagedAlphaValue = $this->api->new('uint8_t');

        $this->api->saucer_webview_background(
            $this->handle,
            \FFI::addr($this->unmanagedRedValue),
            \FFI::addr($this->unmanagedGreenValue),
            \FFI::addr($this->unmanagedBlueValue),
            \FFI::addr($this->unmanagedAlphaValue),
        );
    }

    public function update(?int $red = null, ?int $green = null, ?int $blue = null, ?int $alpha = null): void
    {
        $this->api->saucer_webview_set_background(
            $this->handle,
            /** @phpstan-ignore-next-line : PHPStan false-positive */
            $this->unmanagedRedValue->cdata = $red ?? $this->unmanagedRedValue->cdata,
            /** @phpstan-ignore-next-line : PHPStan false-positive */
            $this->unmanagedGreenValue->cdata = $green ?? $this->unmanagedGreenValue->cdata,
            /** @phpstan-ignore-next-line : PHPStan false-positive */
            $this->unmanagedBlueValue->cdata = $blue ?? $this->unmanagedBlueValue->cdata,
            /** @phpstan-ignore-next-line : PHPStan false-positive */
            $this->unmanagedAlphaValue->cdata = $alpha ?? $this->unmanagedAlphaValue->cdata,
        );
    }
}
