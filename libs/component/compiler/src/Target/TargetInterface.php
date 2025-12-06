<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Target;

use Boson\Component\Compiler\Configuration;

/**
 * @template TCompileResult of mixed = void
 */
interface TargetInterface extends \Stringable
{
    /**
     * Gets a target type
     *
     * @var non-empty-string
     */
    public string $type {
        get;
    }

    /**
     * Gets output directory
     *
     * @var non-empty-string
     */
    public string $output {
        get;
    }

    /**
     * Gets target additional configuration
     *
     * @var array<array-key, mixed>
     */
    public array $config {
        get;
    }

    /**
     * @return non-empty-string
     */
    public function getBuildDirectory(Configuration $config): string;

    /**
     * @return TCompileResult
     */
    public function compile(Configuration $config);
}
