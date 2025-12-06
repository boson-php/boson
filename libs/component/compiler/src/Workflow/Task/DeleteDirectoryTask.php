<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow\Task;

use Boson\Component\Compiler\Configuration;
use Boson\Component\Compiler\Workflow\Task;
use Boson\Component\Compiler\Workflow\Task\Support\Path;

final readonly class DeleteDirectoryTask implements TaskInterface
{
    /**
     * @var non-empty-string
     */
    public string $directory;

    /**
     * @param non-empty-string $directory
     */
    public function __construct(
        string $directory,
        public bool $removeSelf = false,
    ) {
        $this->directory = Path::normalize($directory);
    }

    public function __invoke(Configuration $config): void
    {
        Task::info('Cleanup "%s" directory', [
            Path::simplify($config, $this->directory),
        ]);

        if (!\is_dir($this->directory)) {
            Task::notify('Directory is not defined');

            return;
        }

        foreach (Path::files($this->directory) as $file) {
            $pathname = $file->getPathname();

            if ($pathname === '') {
                continue;
            }

            if ($file->isDir()) {
                $this->removeDirectory($config, $pathname);
            }

            if ($file->isFile()) {
                Task::run($config, new DeleteFileTask($pathname));
            }
        }

        if (!$this->removeSelf) {
            return;
        }

        $this->removeDirectory($config, $this->directory);
    }

    private function removeDirectory(Configuration $config, string $directory): void
    {
        $isDeleted = @\rmdir($directory);

        if (!$isDeleted) {
            throw new \RuntimeException(\sprintf(
                'Could not delete directory "%s"',
                $directory,
            ));
        }

        Task::notify('[removed] "%s"', [
            Path::simplify($config, $directory),
        ]);
    }
}
