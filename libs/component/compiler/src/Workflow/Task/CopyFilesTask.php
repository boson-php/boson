<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow\Task;

use Boson\Component\Compiler\Configuration;
use Boson\Component\Compiler\Workflow\Task;
use Boson\Component\Compiler\Workflow\Task\Support\Path;

final readonly class CopyFilesTask implements TaskInterface
{
    /**
     * @var non-empty-string
     */
    public string $sourceDirectory;

    /**
     * @var non-empty-string
     */
    public string $targetDirectory;

    /**
     * @param non-empty-string $sourceDirectory
     * @param non-empty-string $targetDirectory
     */
    public function __construct(
        string $sourceDirectory,
        string $targetDirectory,
    ) {
        $this->sourceDirectory = Path::normalize($sourceDirectory);
        $this->targetDirectory = Path::normalize($targetDirectory);
    }

    public function __invoke(Configuration $config): void
    {
        Task::info('Copy files from "%s" directory into "%s"', [
            Path::simplify($config, $this->sourceDirectory),
            Path::simplify($config, $this->targetDirectory),
        ]);

        if (!\is_dir($this->sourceDirectory)) {
            Task::notify('Directory is not defined');

            return;
        }

        Task::run($config, new CreateDirectoryTask(
            directory: $this->targetDirectory,
        ));

        foreach (Path::files($this->sourceDirectory) as $file) {
            if ($file->isDir()) {
                continue;
            }

            $relativePathname = \substr(
                string: $file->getPathname(),
                offset: \strlen($this->sourceDirectory),
            );

            $relativeDirectory = \trim(
                string: \dirname($relativePathname),
                characters: \DIRECTORY_SEPARATOR,
            );

            $sourcePathname = $this->sourceDirectory
                . $relativePathname;

            $targetPathname = $relativeDirectory === ''
                ? $this->targetDirectory
                    . \DIRECTORY_SEPARATOR . $file->getFilename()
                : $this->targetDirectory
                    . \DIRECTORY_SEPARATOR . $relativeDirectory
                    . \DIRECTORY_SEPARATOR . $file->getFilename();

            Task::run($config, new CreateDirectoryTask(
                /** @phpstan-ignore-next-line : Directory can't be root */
                directory: \dirname($targetPathname),
            ));

            Task::run($config, new CopyFileTask(
                sourcePathname: $sourcePathname,
                targetPathname: $targetPathname,
            ));
        }

        Task::notify('Files are copied');
    }
}
