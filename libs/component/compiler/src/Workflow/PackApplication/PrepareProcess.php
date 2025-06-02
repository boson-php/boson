<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow\PackApplication;

use Boson\Component\Compiler\Configuration;

final readonly class PrepareProcess
{
    /**
     * @return iterable<mixed, PrepareProcessStatus>
     * @throws \JsonException
     */
    public function process(Configuration $configuration): iterable
    {
        yield from $this->createBuildDirectory($configuration);

        yield from $this->createBoxConfig($configuration);
    }

    /**
     * @return non-empty-string
     */
    private function getEntrypointPathname(Configuration $configuration): string
    {
        $entrypoint = $configuration->root . '/' . $configuration->entrypoint;

        if (($entrypoint = \realpath($entrypoint)) !== false) {
            return $entrypoint;
        }

        $entrypoint = $configuration->entrypoint;

        if (($entrypoint = \realpath($entrypoint)) !== false) {
            return $entrypoint;
        }

        throw new \RuntimeException(\sprintf(
            'Could not find entrypoint file "%s"',
            $configuration->root . \DIRECTORY_SEPARATOR . $configuration->entrypoint
        ));
    }

    /**
     * @return non-empty-string
     */
    private function getValidEntrypointPathname(Configuration $configuration): string
    {
        $entrypoint = $this->getEntrypointPathname($configuration);

        $tokens = \PhpToken::tokenize(\file_get_contents($entrypoint));

        foreach ($tokens as $token) {
            if ($token->is(\T_HALT_COMPILER)) {
                return $entrypoint;
            }
        }

        throw new \RuntimeException('Entrypoint file requires "__halt_compiler()" statement');
    }

    private function getBoxConfig(Configuration $configuration): array
    {
        $finder = [];

        foreach ($configuration->buildFiles as $inclusion) {
            $section = [];

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

        return [
            'base-path' => $configuration->root,
            'check-requirements' => false,
            'dump-autoload' => false,
            'stub' => $this->getValidEntrypointPathname($configuration),
            'output' => $configuration->pharPathname,
            'main' => false,
            'chmod' => '0644',
            'compression' => 'GZ',
            'finder' => $finder,
        ];
    }

    /**
     * @return iterable<mixed, PrepareProcessStatus>
     * @throws \JsonException
     */
    private function createBoxConfig(Configuration $configuration): iterable
    {
        yield PrepareProcessStatus::ReadyToCreateBoxConfig;

        \file_put_contents($configuration->boxConfigPathname, \json_encode(
            value: $this->getBoxConfig($configuration),
            flags: \JSON_PRETTY_PRINT | \JSON_THROW_ON_ERROR,
        ));

        yield PrepareProcessStatus::CreatedBoxConfig;
    }

    /**
     * @return iterable<mixed, PrepareProcessStatus>
     */
    private function createBuildDirectory(Configuration $configuration): iterable
    {
        yield PrepareProcessStatus::ReadyToCreateBuildDirectory;

        if (!\is_dir($configuration->build)) {
            $status = @\mkdir($configuration->build, recursive: true);

            if ($status === false) {
                throw new \RuntimeException(\sprintf(
                    'Could not create build directory "%s"',
                    $configuration->build,
                ));
            }
        }

        yield PrepareProcessStatus::CreatedBuildDirectory;
    }
}
