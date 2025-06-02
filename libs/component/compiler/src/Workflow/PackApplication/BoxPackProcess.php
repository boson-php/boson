<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow\PackApplication;

use Boson\Component\Compiler\Configuration;
use Symfony\Component\Process\Process;

final readonly class BoxPackProcess
{
    /**
     * @return iterable<mixed, BoxDownloadProcessStatus>
     */
    public function process(Configuration $configuration): iterable
    {
        yield BoxPackProcessStatus::ReadyToPack;

        $process = $this->getProcess($configuration);
        $process->start();

        $error = '';

        foreach ($process as $status => $data) {
            if ($status === Process::ERR) {
                $error .= $data;
            }

            yield $data => BoxPackProcessStatus::Packing;
        }

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(\trim($error));
        }

        yield BoxPackProcessStatus::Packed;
    }

    private function getProcess(Configuration $configuration): Process
    {
        return new Process(
            command: [
                \PHP_BINARY,
                $configuration->boxPharPathname,
                'compile',
                '--config=' . $configuration->boxConfigPathname,
            ],
            cwd: $configuration->root,
        );
    }
}
