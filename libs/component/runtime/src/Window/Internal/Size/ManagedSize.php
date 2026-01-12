<?php

declare(strict_types=1);

namespace Boson\Window\Internal\Size;

use Boson\Component\Saucer\SaucerInterface;
use Boson\Window\MutableSizeInterface;
use Boson\Window\Size\SizeStringableImpl;
use FFI\CData;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Window
 */
abstract class ManagedSize implements MutableSizeInterface
{
    use SizeStringableImpl;

    /**
     * @var int<0, 2147483647>
     */
    public int $width {
        get {
            $this->syncSizeValues();

            /** @var int<0, 2147483647> */
            return $this->widthValue->cdata;
        }
        set {
            $this->update(width: $value);
        }
    }

    private readonly CData $widthValue;

    /**
     * @var int<0, 2147483647>
     */
    public int $height {
        get {
            $this->syncSizeValues();

            /** @var int<0, 2147483647> */
            return $this->heightValue->cdata;
        }
        set {
            $this->update(height: $value);
        }
    }

    private readonly CData $heightValue;

    public function __construct(
        protected readonly SaucerInterface $api,
        protected readonly CData $handle,
    ) {
        $this->widthValue = $this->api->new('int');
        $this->heightValue = $this->api->new('int');
    }

    protected function syncSizeValues(): void
    {
        $this->getCurrentSizeValuesByRef(
            width: \FFI::addr($this->widthValue),
            height: \FFI::addr($this->heightValue),
        );
    }

    abstract protected function getCurrentSizeValuesByRef(CData $width, CData $height): void;

    public function update(?int $width = null, ?int $height = null): void
    {
        if ($width === null && $height === null) {
            return;
        }

        $this->setSizeValues(
            width: $width ?? $this->width,
            height: $height ?? $this->height,
        );
    }

    /**
     * @param int<0, 2147483647> $width
     * @param int<0, 2147483647> $height
     */
    abstract protected function setSizeValues(int $width, int $height): void;

    public function __debugInfo(): array
    {
        return [
            'width' => $this->width,
            'height' => $this->height,
        ];
    }
}
