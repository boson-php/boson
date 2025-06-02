<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Command\PackCommand;

use Boson\Component\Compiler\Configuration;
use Boson\Component\Compiler\Workflow\PackApplication\BoxDownloadProcessStatus;
use Boson\Component\Compiler\Workflow\PackApplication\BoxPackProcessStatus;
use Boson\Component\Compiler\Workflow\PackApplication\PrepareProcessStatus;
use Boson\Component\Compiler\Workflow\PackApplicationWorkflow;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

final readonly class PackApplicationWorkflowPresenter
{
    private PackApplicationWorkflow $workflow;

    public function __construct()
    {
        $this->workflow = new PackApplicationWorkflow();
    }

    public function process(Configuration $config, OutputInterface $output): void
    {
        $progress = new ProgressBar($output);
        $progress->setFormat('[%bar%] %message%');

        foreach ($this->workflow->process($config) as $process) {
            switch ($process) {
                case PrepareProcessStatus::ReadyToCreateBuildDirectory:
                    $output->write(' · Checking build directory');
                    break;

                case PrepareProcessStatus::CreatedBuildDirectory:
                    $output->writeln(\sprintf(
                        "\33[2K\r <info>✓</info> Build directory \"<comment>%s</comment>\" is available",
                        $config->build,
                    ));
                    break;

                case PrepareProcessStatus::ReadyToCreateBoxConfig:
                    $output->write(' · Checking box config');
                    break;

                case PrepareProcessStatus::CreatedBoxConfig:
                    $output->writeln(\sprintf(
                        "\33[2K\r <info>✓</info> Config \"<comment>%s</comment>\" is created",
                        $config->boxConfigPathname,
                    ));
                    break;

                case BoxDownloadProcessStatus::ReadyToDownload:
                    $output->write(' · Checking <comment>humbug/box</comment> installation');
                    break;

                case BoxDownloadProcessStatus::Downloading:
                    $progress->setMessage('Downloading <comment>humbug/box</comment>');
                    $progress->advance();
                    break;

                case BoxDownloadProcessStatus::Complete:
                    $progress->clear();
                    $output->writeln(\sprintf(
                        "\33[2K\r <info>✓</info> The \"<comment>humbug/box</comment>\" <info>v%s</info> is ready",
                        $config->boxVersion,
                    ));
                    break;

                case BoxPackProcessStatus::ReadyToPack:
                    $output->write(' · Packing application');
                    break;

                case BoxPackProcessStatus::Packing:
                    $progress->setMessage('Packing application');
                    $progress->advance();
                    break;

                case BoxPackProcessStatus::Packed:
                    $progress->clear();
                    $output->writeln(\sprintf(
                        "\33[2K\r <info>✓</info> Application packed \"<comment>%s</comment>\"",
                        $config->pharPathname,
                    ));
                    break;
            }
        }
    }
}
