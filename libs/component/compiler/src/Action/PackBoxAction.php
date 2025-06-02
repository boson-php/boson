<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Action;

use Boson\Component\Compiler\Configuration;
use Symfony\Component\Process\Process;

/**
 * @template-implements ActionInterface<PackBoxStatus>
 */
final readonly class PackBoxAction implements ActionInterface
{
    public function process(Configuration $config): iterable
    {
        yield PackBoxStatus::ReadyToPack;

        $process = $this->createProcess($config);
        $process->start();

        $error = '';

        foreach ($process as $status => $data) {
            if ($status === Process::ERR) {
                $error .= $data;
            }

            yield $data => PackBoxStatus::Packing;
        }

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(\trim($error));
        }

        yield PackBoxStatus::Packed;
    }

    private function createProcess(Configuration $config): Process
    {
        return new Process(
            command: [
                \PHP_BINARY,
                $config->boxPharPathname,
                'compile',
                '--config=' . $config->boxConfigPathname,
            ],
            cwd: $config->root,
        );
    }
}
