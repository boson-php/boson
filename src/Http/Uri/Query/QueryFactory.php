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
     * @return array<non-empty-string, string|array<array-key, string>>
     */
    private function components(string $query): array
    {
        \parse_str($query, $components);

        /** @var array<non-empty-string, string|array<array-key, string>> */
        return $components;
    }
}
