<?php

declare(strict_types=1);

namespace Boson\Http\Uri\Path;

use Boson\Http\Uri\Path;
use Boson\Http\Uri\PathInterface;

final readonly class PathFactory implements PathFactoryInterface
{
    /**
     * @var non-empty-string
     */
    private const string SEGMENT_DELIMITER = PathInterface::SEGMENT_DELIMITER;

    public function createPathFromString(string $path): PathInterface
    {
        return new Path(self::segments($path));
    }

    /**
     * @return list<non-empty-string>
     */
    private static function segments(string $path): array
    {
        $result = [];

        foreach (\explode(self::SEGMENT_DELIMITER, $path) as $segment) {
            if ($segment !== '') {
                $result[] = \urldecode($segment);
            }
        }

        return $result;
    }
}
