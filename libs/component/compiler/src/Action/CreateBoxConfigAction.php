<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Action;

use Boson\Component\Compiler\Configuration;
use Boson\Component\Compiler\Configuration\DirectoryIncludeConfiguration;
use Boson\Component\Compiler\Configuration\FileIncludeConfiguration;
use Boson\Component\Compiler\Configuration\FinderIncludeConfiguration;

/**
 * @template-implements ActionInterface<CreateBoxConfigStatus>
 */
final readonly class CreateBoxConfigAction implements ActionInterface
{
    public function process(Configuration $config): iterable
    {
        yield CreateBoxConfigStatus::ReadyToCreate;

        \file_put_contents($config->boxConfigPathname, \json_encode(
            value: $this->getBoxConfig($config),
            flags: \JSON_PRETTY_PRINT | \JSON_THROW_ON_ERROR,
        ));

        yield CreateBoxConfigStatus::Created;
    }

    /**
     * @return list<array{
     *     in: non-empty-list<non-empty-string>,
     *     name?: list<non-empty-string>,
     *     exclude?: list<non-empty-string>,
     *     notName?: list<non-empty-string>,
     * }>
     */
    private function getBoxFinderConfig(Configuration $config): array
    {
        $finder = [];

        foreach ($config->build as $inclusion) {
            if (!$inclusion instanceof FinderIncludeConfiguration) {
                continue;
            }

            if ($inclusion->directories === []) {
                throw new \InvalidArgumentException(\sprintf(
                    'Directories list cannot be empty in "build.finder[%s].directories" config section',
                    \count($finder),
                ));
            }

            $section = ['in' => $inclusion->directories];

            if ($inclusion->notDirectories !== []) {
                $section['exclude'] = $inclusion->notDirectories;
            }

            if ($inclusion->names !== []) {
                $section['name'] = $inclusion->names;
            }

            if ($inclusion->notNames !== []) {
                $section['notName'] = $inclusion->notNames;
            }

            $finder[] = $section;
        }

        return $finder;
    }

    /**
     * @return list<non-empty-string>
     */
    private function getBoxFilesConfig(Configuration $config): array
    {
        $files = [];

        foreach ($config->build as $inclusion) {
            if (!$inclusion instanceof FileIncludeConfiguration) {
                continue;
            }

            $files[] = $inclusion->pathname;
        }

        return $files;
    }

    /**
     * @return list<non-empty-string>
     */
    private function getBoxDirectoriesConfig(Configuration $config): array
    {
        $directories = [];

        foreach ($config->build as $inclusion) {
            if (!$inclusion instanceof DirectoryIncludeConfiguration) {
                continue;
            }

            $directories[] = $inclusion->directory;
        }

        return $directories;
    }

    /**
     * @return array<non-empty-string, mixed>
     */
    private function getBoxConfig(Configuration $config): array
    {
        $finder = $this->getBoxFinderConfig($config);
        $directories = $this->getBoxDirectoriesConfig($config);
        $files = $this->getBoxFilesConfig($config);
        $files[] = $config->entrypoint;

        return [
            'base-path' => $config->root,
            'check-requirements' => false,
            'dump-autoload' => false,
            'stub' => $config->boxStubPathname,
            'output' => $config->pharPathname,
            'main' => false,
            'chmod' => '0644',
            'compression' => 'GZ',
            'finder' => $finder,
            'files' => $files,
            'directories' => $directories,
        ];
    }
}
