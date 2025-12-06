<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow\Task;

use Boson\Component\Compiler\Configuration;
use Boson\Component\Compiler\Target\TargetInterface;
use Boson\Component\Compiler\Workflow\Task;

/**
 * @template-implements TaskInterface<non-empty-string|null>
 */
final readonly class FindCustomSfxPathnameTask implements TaskInterface
{
    /**
     * @var non-empty-string
     */
    public const string DEFAULT_CUSTOM_SFX_KEY = 'sfx';

    public function __construct(
        public TargetInterface $target,
        /**
         * @var non-empty-string
         */
        public string $customSfxConfigKey = self::DEFAULT_CUSTOM_SFX_KEY,
    ) {}

    /**
     * @phpstan-assert non-empty-string $sfx
     */
    private function assertValidSfxValue(mixed $sfx): void
    {
        if (\is_string($sfx) && $sfx !== '') {
            return;
        }

        throw new \InvalidArgumentException(\sprintf(
            'Custom SFX of %s compilation target must be a non empty string, %s given',
            $this->target->type,
            \get_debug_type($sfx),
        ));
    }

    public function __invoke(Configuration $config): ?string
    {
        Task::info('Lookup for custom SFX');

        $sfx = $this->target->config['sfx'] ?? null;

        if (!isset($sfx)) {
            Task::notify('Custom SFX not defined');

            return null;
        }

        $this->assertValidSfxValue($sfx);

        if (\is_readable($resolved = $config->root . \DIRECTORY_SEPARATOR . $sfx)) {
            return $resolved;
        }

        if (\is_readable($sfx)) {
            return $sfx;
        }

        throw new \InvalidArgumentException(\sprintf(
            'Custom SFX "%s" of %s compilation target must be a valid pathname to the file',
            $sfx,
            $this->target->type,
        ));
    }
}
