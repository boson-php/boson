<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Action;

use Boson\Component\Compiler\Configuration;

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
     * @return non-empty-string
     */
    private function getEntrypointPathname(Configuration $config): string
    {
        $entrypoints = [
            $config->root . '/' . $config->entrypoint,
            $config->entrypoint,
        ];

        foreach ($entrypoints as $entrypoint) {
            $realpath = \realpath($entrypoint);

            if ($realpath !== false) {
                return $realpath;
            }
        }

        throw new \RuntimeException(\sprintf(
            'Could not find entrypoint file "%s"',
            $config->root . \DIRECTORY_SEPARATOR . $config->entrypoint,
        ));
    }

    private function validateEntrypoint(string $entrypoint): void
    {
        $tokens = \PhpToken::tokenize(\file_get_contents($entrypoint));

        foreach ($tokens as $token) {
            if ($token->is(\T_HALT_COMPILER)) {
                return;
            }
        }

        throw new \RuntimeException(\sprintf(
            'Entrypoint file "%s" requires "__halt_compiler()" PHP statement',
            $entrypoint,
        ));
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

        $entrypoint = $this->getEntrypointPathname($configuration);

        $this->validateEntrypoint($entrypoint);

        return [
            'base-path' => $configuration->root,
            'check-requirements' => false,
            'dump-autoload' => false,
            'stub' => $entrypoint,
            'output' => $configuration->pharPathname,
            'main' => false,
            'chmod' => '0644',
            'compression' => 'GZ',
            'finder' => $finder,
        ];
    }
}
