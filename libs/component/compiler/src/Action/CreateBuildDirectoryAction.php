<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Action;

use Boson\Component\Compiler\Configuration;

/**
 * @template-implements ActionInterface<CreateBuildDirectoryStatus>
 */
final readonly class CreateBuildDirectoryAction implements ActionInterface
{
    public function process(Configuration $config): iterable
    {
        yield CreateBuildDirectoryStatus::ReadyToCreate;

        $directory = $config->build;

        if (!\is_dir($directory)) {
            $this->createOrFail($directory);
        }

        yield CreateBuildDirectoryStatus::Created;
    }

    private function createOrFail(string $directory): void
    {
        $status = @\mkdir($directory, recursive: true);

        if ($status === true) {
            return;
        }

        throw new \RuntimeException(\sprintf(
            'Could not create build directory "%s"',
            $directory,
        ));
    }
}
