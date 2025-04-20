<?php

declare(strict_types=1);

namespace Boson\Window\Color;

use Boson\Window\Color;
use Boson\Window\ColorInterface;

final class HexColorFactory implements ColorFactoryInterface
{
    public function createFromString(string $color): ?ColorInterface
    {
        \preg_match('/^#([a-f0-9]{3,8})$/isum', $color, $matches);

        /** @var array{}|array{non-empty-string,non-empty-string} $matches */
        if ($matches === []) {
            return null;
        }

        return match (\strlen($value = $matches[1])) {
            // #RGB format
            3 => new Color(
                /** @phpstan-ignore-next-line : A-F provides int<0, 16> */
                red: (int) \hexdec($value[0] . $value[0]),
                /** @phpstan-ignore-next-line : A-F provides int<0, 16> */
                green: (int) \hexdec($value[1] . $value[1]),
                /** @phpstan-ignore-next-line : A-F provides int<0, 16> */
                blue: (int) \hexdec($value[2] . $value[2]),
            ),
            // #RGBA format
            4 => new Color(
                /** @phpstan-ignore-next-line : A-F provides int<0, 16> */
                red: (int) \hexdec($value[0] . $value[0]),
                /** @phpstan-ignore-next-line : A-F provides int<0, 16> */
                green: (int) \hexdec($value[1] . $value[1]),
                /** @phpstan-ignore-next-line : A-F provides int<0, 16> */
                blue: (int) \hexdec($value[2] . $value[2]),
                /** @phpstan-ignore-next-line : A-F provides int<0, 16> */
                alpha: (int) \hexdec($value[3] . $value[3]),
            ),
            // #RRGGBB format
            6 => new Color(
                /** @phpstan-ignore-next-line : A-F provides int<0, 16> */
                red: (int) \hexdec($value[0] . $value[1]),
                /** @phpstan-ignore-next-line : A-F provides int<0, 16> */
                green: (int) \hexdec($value[2] . $value[3]),
                /** @phpstan-ignore-next-line : A-F provides int<0, 16> */
                blue: (int) \hexdec($value[4] . $value[5]),
            ),
            // #RRGGBBAA format
            8 => new Color(
                /** @phpstan-ignore-next-line : A-F provides int<0, 16> */
                red: (int) \hexdec($value[0] . $value[1]),
                /** @phpstan-ignore-next-line : A-F provides int<0, 16> */
                green: (int) \hexdec($value[2] . $value[3]),
                /** @phpstan-ignore-next-line : A-F provides int<0, 16> */
                blue: (int) \hexdec($value[4] . $value[5]),
                /** @phpstan-ignore-next-line : A-F provides int<0, 16> */
                alpha: (int) \hexdec($value[6] . $value[7]),
            ),
            default => null,
        };
    }
}
