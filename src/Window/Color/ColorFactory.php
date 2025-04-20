<?php

declare(strict_types=1);

namespace Boson\Window\Color;

use Boson\Window\ColorInterface;

final class ColorFactory implements ColorFactoryInterface
{
    /**
     * @var list<ColorFactoryInterface>
     */
    private array $factories;

    /**
     * @param iterable<mixed, ColorFactoryInterface> $factories
     */
    public function __construct(
        iterable $factories = [
            new HexColorFactory(),
        ],
    ) {
        $this->factories = \iterator_to_array($factories, false);
    }

    public function createFromString(string $color): ?ColorInterface
    {
        foreach ($this->factories as $factory) {
            $result = $factory->createFromString($color);

            if ($result instanceof ColorInterface) {
                return $result;
            }
        }

        return null;
    }
}
