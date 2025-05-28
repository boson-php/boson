<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Internal;

/**
 * @internal this is an internal library class, please do not use it in your code.
 * @psalm-internal Boson\Component\CpuInfo
 */
final class LinuxProcCpuInfo
{
    /**
     * @var non-empty-string
     */
    private const string DEFAULT_CPU_INFO_FILE = '/proc/cpuinfo';

    /**
     * @var array<array-key, array<non-empty-string, string>>|null
     */
    private ?array $segments = null;

    public static function isReadable(): bool
    {
        return \is_readable(self::DEFAULT_CPU_INFO_FILE);
    }

    /**
     * @return array<numeric-string|int, list<array<non-empty-string, string>>>
     */
    public function getSegmentsByPhysicalId(): array
    {
        $result = [];

        foreach ($this->getSegments() as $segment) {
            $id = $segment['physical id'] ?? null;

            if ($id === null) {
                continue;
            }

            $result[$id][] = $segment;
        }

        return $result;
    }

    /**
     * @return list<array<non-empty-string, string>>
     */
    public function getSegments(): array
    {
        if (!\is_readable(self::DEFAULT_CPU_INFO_FILE)) {
            return [];
        }

        return $this->segments ??= $this->readAsSegments();
    }

    /**
     * @return iterable<array-key, string>
     */
    private function read(): iterable
    {
        $proc = @\fopen(self::DEFAULT_CPU_INFO_FILE, 'rb');

        if ($proc === false) {
            return [];
        }

        while (!\feof($proc)) {
            yield (string) \fgets($proc);
        }

        \fclose($proc);
    }

    /**
     * @return array<array-key, array<non-empty-string, string>>
     */
    private function readAsSegments(): array
    {
        $segments = $current = [];

        foreach ($this->read() as $line) {
            $offset = \strpos($line, ':');

            if ($offset === false) {
                if ($current !== []) {
                    $segments[] = $current;
                    $current = [];
                }

                continue;
            }

            $key = \trim(\substr($line, 0, $offset));

            if ($key === '') {
                continue;
            }

            $value = \trim(\substr($line, $offset + 1));

            $current[$key] = $value;
        }

        if ($current !== []) {
            $segments[] = $current;
        }

        return $segments;
    }
}
