<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow\CompileApplication;

use Boson\Component\Compiler\Assembly\Assembly;
use Boson\Component\Compiler\Configuration;

final readonly class PrepareProcess
{
    /**
     * @return iterable<mixed, PrepareProcessStatus>
     */
    public function process(Configuration $configuration, Assembly $assembly): iterable
    {
        yield from $this->cleanupBuildDirectory($configuration, $assembly);
        yield from $this->createBuildDirectory($configuration, $assembly);
    }

    /**
     * @return iterable<Assembly|string, PrepareProcessStatus>
     */
    private function cleanupBuildDirectory(Configuration $configuration, Assembly $assembly): iterable
    {
        yield $assembly => PrepareProcessStatus::ReadyToCleanBuildDirectory;

        $directory = $assembly->getBuildDirectory($configuration);

        if (\is_dir($directory)) {
            $files = new \RecursiveIteratorIterator(
                iterator: new \RecursiveDirectoryIterator(
                    directory: $directory,
                    flags: \FilesystemIterator::SKIP_DOTS,
                ),
                mode: \RecursiveIteratorIterator::CHILD_FIRST,
            );

            /** @var \SplFileInfo $file */
            foreach ($files as $file) {
                if ($file->isDir()) {
                    yield $file->getPathname() => PrepareProcessStatus::CleaningBuildDirectory;

                    \rmdir($file->getPathname());
                }

                if ($file->isFile()) {
                    yield $file->getPathname() => PrepareProcessStatus::CleaningBuildDirectory;

                    \unlink($file->getPathname());
                }
            }

            yield $directory => PrepareProcessStatus::CleaningBuildDirectory;

            \rmdir($directory);
        }

        yield $assembly => PrepareProcessStatus::CleanedBuildDirectory;
    }

    /**
     * @return iterable<Assembly, PrepareProcessStatus>
     */
    private function createBuildDirectory(Configuration $configuration, Assembly $assembly): iterable
    {
        yield $assembly => PrepareProcessStatus::ReadyToCreateBuildDirectory;

        $directory = $assembly->getBuildDirectory($configuration);

        if (!\is_dir($directory)) {
            $status = @\mkdir($directory, recursive: true);

            if ($status === false) {
                throw new \RuntimeException(\sprintf(
                    'Could not create build directory "%s" for assembly "%s"',
                    $directory,
                    (string) $assembly,
                ));
            }
        }

        yield $assembly => PrepareProcessStatus::CreatedBuildDirectory;
    }
}
