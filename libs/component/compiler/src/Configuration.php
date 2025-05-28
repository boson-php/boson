<?php

declare(strict_types=1);

namespace Boson\Component\Compiler;


final class Configuration
{
    public array $files {
        get => array_filter($this->includes, static function ($include) {
            return pathinfo($include, PATHINFO_EXTENSION) !== '';
        });
    }

    public array $directories {
        get => array_filter($this->includes, static function ($include) {
            return pathinfo($include, PATHINFO_EXTENSION) === '';
        });
    }

    public function __construct(
        public readonly string $name,
        public readonly string $entrypoint,
        public readonly array $includes,
    ) {}

    /**
     * @param  array{name: string, entrypoint: string, includes: string[]}  $config
     *
     */
    public static function fromArray(array $config): self
    {
        return new self(
            $config['name'] ?? 'app',
            $config['entrypoint'] ?? 'index.php',
            $config['includes'] ?? [],
        );
    }
}
