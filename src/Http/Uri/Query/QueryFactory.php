<?php

declare(strict_types=1);

namespace Boson\Http\Uri\Query;

use Boson\Http\Uri\Query;
use Boson\Http\Uri\QueryInterface;

final class QueryFactory implements QueryFactoryInterface
{
    public function createQueryFromString(string $query): QueryInterface
    {
        return new Query(self::components($query));
    }

    /**
     * @return iterable<non-empty-string, string>
     */
    private function components(string $query): iterable
    {
        foreach (self::params($query) as $parameter) {
            /** @var non-empty-list<string> $segments */
            $segments = \explode(QueryInterface::KV_DELIMITER, $parameter);

            if (($key = \array_shift($segments)) === '') {
                continue;
            }

            $value = \implode(QueryInterface::KV_DELIMITER, $segments);

            yield \urldecode($key) => \urldecode($value);
        }
    }

    /**
     * @return iterable<array-key, non-empty-string>
     */
    private static function params(string $query): iterable
    {
        $parameters = \explode(QueryInterface::SEGMENT_DELIMITER, $query);

        foreach ($parameters as $parameter) {
            if ($parameter === '') {
                continue;
            }

            yield $parameter;
        }
    }
}
