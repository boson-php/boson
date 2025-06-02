<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends SymfonyCommand
{
    protected function fail(OutputInterface $output, \Throwable $e): int
    {
        $messages = \explode("\n", \trim(\wordwrap($e->getMessage())));

        $length = 0;
        foreach ($messages as $i => $message) {
            $messages[$i] = $message = \rtrim(\trim($message, "\r"));

            $length = \max($length, \strlen($message));
        }

        $delimiter = \str_repeat(' ', $length);

        $output->writeln('');
        $output->writeln('<error>   ' . $delimiter . '   </error>');

        foreach ($messages as $message) {
            $suffix = \str_repeat(' ', $length - \strlen($message));
            $output->writeln('<error>   ' . $message . $suffix . '   </error>');
        }

        $output->writeln('<error>   ' . $delimiter . '   </error>');

        return self::FAILURE;
    }
}
