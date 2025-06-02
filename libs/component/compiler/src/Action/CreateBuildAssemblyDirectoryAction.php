<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Action;

use Boson\Component\Compiler\Configuration;

/**
 * @template-extends AssemblyAction<CreateBuildDirectoryStatus>
 */
final readonly class CreateBuildAssemblyDirectoryAction extends AssemblyAction
{
    public function process(Configuration $config): iterable
    {
        yield $this->assembly => CreateBuildDirectoryStatus::ReadyToCreate;

        $directory = $this->assembly->getBuildDirectory($config);

        if (!\is_dir($directory)) {
            $this->createOrFail($directory);
        }

        yield $this->assembly => CreateBuildDirectoryStatus::Created;
    }

    private function createOrFail(string $directory): void
    {
        $status = @\mkdir($directory, recursive: true);

        if ($status === true) {
            return;
        }

        throw new \RuntimeException(\sprintf(
            'Could not create build directory "%s" for assembly "%s"',
            $directory,
            $this->assembly,
        ));
    }
}
