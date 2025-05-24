<?php

declare(strict_types=1);

namespace Boson\Http;

class MutableHeadersMap extends HeadersMap implements
    MutableHeadersInterface
{
    public function set(string $name, string $value): void
    {
        $this->lines[self::getFormattedHeaderName($name)] = [$value];
    }

    public function add(string $name, string $value): void
    {
        $this->lines[self::getFormattedHeaderName($name)][] = $value;
    }

    public function remove(string $name): void
    {
        unset($this->lines[self::getFormattedHeaderName($name)]);
    }

    public function removeAll(): void
    {
        $this->lines = [];
    }
}
