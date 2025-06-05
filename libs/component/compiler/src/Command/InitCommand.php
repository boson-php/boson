<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

#[AsCommand(name: 'init', description: 'Initialize configuration')]
final class InitCommand extends ConfigAwareCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = $this->getConfiguration($input);

        $boson = $config->root . '/boson.json';

        if (\is_file($boson)) {
            $output->writeln(\sprintf('The config "<comment>%s</comment>" already exists', $boson));

            $helper = $this->getHelper('question');

            if (!$helper instanceof QuestionHelper) {
                throw new \InvalidArgumentException('Could not initialize question helper');
            }

            $shouldContinue = $helper->ask($input, $output, new ConfirmationQuestion(
                question: ' · Do you want to overwrite it? [y/N] ',
                default: false,
                trueAnswerRegex: '/^(y|j)/i'
            ));

            if ($shouldContinue === false) {
                return self::SUCCESS;
            }
        }

        \file_put_contents($boson, \json_encode([
            'name' => 'app',
            'arch' => ['amd64', 'aarch64'],
            'platform' => ['windows', 'linux', 'macos'],
            'entrypoint' => 'index.php',
            'output' => './build',
            'build' => [
                'directories' => [
                    'public',
                ],
                'finder' => [
                    [
                        'directory' => 'src',
                        'name' => '*.php',
                    ],
                    [
                        'directory' => 'vendor',
                        'name' => '*.php',
                    ],
                    [
                        'directory' => 'vendor/boson-php/runtime/resources/dist',
                        'name' => '*.js'
                    ]
                ],
            ],
            'ini' => [
                'memory_limit' => '128M',
            ],
        ], \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT));

        $output->writeln(\sprintf(
            ' <info>●</info> Configuration "<comment>%s</comment>" was successfully created',
            $boson,
        ));

        return self::SUCCESS;
    }
}
