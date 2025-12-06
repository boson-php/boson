<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow\Task;

use Boson\Component\Compiler\Configuration;
use Boson\Component\Compiler\Workflow\Task;
use Boson\Component\Compiler\Workflow\Task\Support\Path;

final readonly class CreateFileTask implements TaskInterface
{
    /**
     * @var non-empty-string
     */
    public string $pathname;

    /**
     * @param non-empty-string $pathname
     */
    public function __construct(
        string $pathname,
        public string $content,
        private bool $overwrite = false,
        private bool $writable = true,
    ) {
        $this->pathname = Path::normalize($pathname);
    }

    private function createFileOrFail(): void
    {
        $written = @\file_put_contents($this->pathname, $this->content);

        if ($written !== false) {
            return;
        }

        throw new \RuntimeException(\sprintf(
            'Could not create file "%s"',
            $this->pathname,
        ));
    }

    public function __invoke(Configuration $config): void
    {
        Task::info('Create "%s" file', [
            Path::simplify($config, $this->pathname),
        ]);

        if ($this->overwrite || !\is_file($this->pathname)) {
            $this->createFileOrFail();
        }

        if ($this->writable) {
            Task::run($config, new ApplyPermissionsTask($this->pathname));
        }

        Task::notify('File has been created');
    }
}
