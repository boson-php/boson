<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Command;

use Boson\Component\Compiler\Command\PackCommand\PackApplicationWorkflowPresenter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'pack', description: 'Pack application files to phar assembly')]
final class PackCommand extends ConfigAwareCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = $this->getConfiguration($input);

        $presenter = new PackApplicationWorkflowPresenter();

        try {
            $presenter->process($config, $output);
        } catch (\Throwable $e) {
            return $this->fail($output, $e);
        }

        return self::SUCCESS;
    }
}
