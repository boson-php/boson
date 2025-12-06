<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow\Task;

use Boson\Component\Compiler\Configuration;
use Boson\Component\Compiler\Workflow\Task;

/**
 * @template-implements TaskInterface<non-empty-string>
 */
final readonly class SelectEditionTask implements TaskInterface
{
    /**
     * @var array<non-empty-string, list<non-empty-lowercase-string>>
     */
    public array $extensions;

    /**
     * A list of built-in extensions that are always included with PHP builds.
     *
     * @var non-empty-list<non-empty-lowercase-string>
     */
    private const array BUILTIN_EXTENSIONS = [
        'core',
        'date',
        'hash',
        'json',
        'pcre',
        'random',
        'reflection',
        'spl',
        'standard',
    ];

    /**
     * @var non-empty-list<non-empty-lowercase-string>
     */
    private const array REQUIRED_EXTENSIONS = [
        'ffi',
        'phar',
    ];

    /**
     * @param iterable<non-empty-string, list<non-empty-lowercase-string>> $extensions
     */
    public function __construct(
        iterable $extensions = [],
    ) {
        $this->extensions = \iterator_to_array($extensions, true);
    }

    /**
     * @param list<non-empty-string> $actual
     */
    protected function isExtensionMatches(Configuration $config, array $actual): bool
    {
        $actual = \array_unique($actual);

        foreach ($config->extensions as $expected) {
            $lower = \strtolower($expected);

            if (\in_array($lower, self::BUILTIN_EXTENSIONS, true)) {
                continue;
            }

            if (!\in_array($lower, $actual, true)) {
                return false;
            }
        }

        return true;
    }

    public function __invoke(Configuration $config): string
    {
        Task::info('Select build for [%s] extensions', [
            \implode(', ', $this->getExpectedDependencies($config)),
        ]);

        foreach ($this->extensions as $name => $extensions) {
            Task::notify('Check %s build', [$name]);

            if ($this->isExtensionMatches($config, $extensions)) {
                Task::notify('Select %s build', [$name]);

                return $name;
            }
        }

        throw $this->missingExtensionsError($config, $extensions ?? self::REQUIRED_EXTENSIONS);
    }

    /**
     * @param list<non-empty-string> $actual
     */
    protected function missingExtensionsError(Configuration $config, array $actual): \Throwable
    {
        $missing = \implode(', ', $this->getMissingDependencies(
            config: $config,
            actual: $actual,
        ));

        $expected = \implode(',', $this->getExpectedDependencies($config));

        return new \RuntimeException(\sprintf(
            <<<'MESSAGE'
                An expected [%s] extensions not supported by this compile target, please build SFX manually:
                - Fork this repository: https://github.com/boson-php/backend-src
                - Open GitHub Actions: https://github.com/USERNAME/backend-src/actions
                - Select expected workflow
                  - "CI on Unix" for Linux and/or macOS
                  - "CI on x86_64 Windows" for Windows
                - Press "Run workflow" dropdown
                - Insert "%s" into "extensions to build" text input
                - Press "Run workflow" dropdown button
                - Download compiled SFX assembly
                - Add SFX assembly to "sfx" configuration section of this compile target
                MESSAGE,
            $missing,
            $expected,
        ));
    }

    /**
     * @param list<non-empty-string> $actual
     *
     * @return list<non-empty-string>
     */
    protected function getMissingDependencies(Configuration $config, array $actual): array
    {
        $missing = \array_values(\array_diff($config->extensions, \array_unique($actual)));

        return $this->exceptBuiltinExtensions($missing);
    }

    /**
     * @return list<non-empty-string>
     */
    protected function getExpectedDependencies(Configuration $config): array
    {
        return $this->exceptBuiltinExtensions(\array_unique([
            ...self::REQUIRED_EXTENSIONS,
            ...$config->extensions,
        ]));
    }

    /**
     * @param array<mixed, non-empty-string> $extensions
     *
     * @return list<non-empty-string>
     */
    protected function exceptBuiltinExtensions(array $extensions): array
    {
        return \array_values(\array_diff($extensions, self::BUILTIN_EXTENSIONS));
    }
}
