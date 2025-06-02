<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Action;

use Boson\Component\Compiler\Configuration;
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

    private function getBoxFinderConfig(Configuration $config): array
    {
        $finder = [];

        foreach ($config->buildFiles as $inclusion) {
            $section = [];

            if (!$inclusion instanceof FinderIncludeConfiguration) {
                continue;
            }

            if ($inclusion->name !== null) {
                $section['name'] = $inclusion->name;
            }

            if ($inclusion->directory !== null) {
                $section['in'] = $inclusion->directory;
            }

            if ($section !== []) {
                $finder[] = $section;
            }
        }

        return $finder;
    }

    private function getBoxFilesConfig(Configuration $config): array
    {
        $files = [];

        foreach ($config->buildFiles as $inclusion) {
            if (!$inclusion instanceof FileIncludeConfiguration) {
                continue;
            }

            $files[] = $inclusion->pathname;
        }

        return $files;
    }

    private function getBoxConfig(Configuration $config): array
    {
        $finder = $this->getBoxFinderConfig($config);

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
        ];
    }
}
