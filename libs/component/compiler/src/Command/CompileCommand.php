<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Command;

use Boson\Component\Compiler\Action\ClearBuildAssemblyDirectoryStatus;
use Boson\Component\Compiler\Action\CompileStatus;
use Boson\Component\Compiler\Action\CreateBuildAssemblyDirectoryStatus;
use Boson\Component\Compiler\Assembly\AssemblyCollection;
use Boson\Component\Compiler\Assembly\Edition;
use Boson\Component\Compiler\Command\PackCommand\PackApplicationWorkflowPresenter;
use Boson\Component\Compiler\Workflow\CompileApplicationWorkflow;
use Boson\Component\CpuInfo\Architecture;
use Boson\Component\OsInfo\Family;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'compile', description: 'Compile application to executable binary')]
final class CompileCommand extends ConfigAwareCommand
{
    protected function configure(): void
    {
        parent::configure();

        $assemblies = AssemblyCollection::createFromBuiltinAssemblies();

        $this->addOption(
            name: 'platform',
            shortcut: 'p',
            mode: InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
            description: 'Target platform (OS family) to built',
            default: [],
            suggestedValues: \array_map(\strval(...), $assemblies->getAvailableFamilies()),
        );

        $this->addOption(
            name: 'arch',
            shortcut: 'a',
            mode: InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
            description: 'Target CPU architecture to built',
            default: [],
            suggestedValues: \array_map(\strval(...), $assemblies->getAvailableArchitectures()),
        );

        $this->addOption(
            name: 'edition',
            shortcut: 'e',
            mode: InputOption::VALUE_REQUIRED,
            description: 'PHP edition (different set of extensions) for assembly',
            default: 'minimal',
            suggestedValues: \array_map(\strval(...), $assemblies->getAvailableEditions()),
        );

        $this->addOption(
            name: 'no-pack',
            shortcut: 'np',
            mode: InputOption::VALUE_NONE,
            description: 'Only compilation is performed without source packing',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = $this->getConfiguration($input);

        // ---------------------------------------------------------------------
        //  Pack Workflow
        // ---------------------------------------------------------------------
        if ($input->getOption('no-pack') !== true) {
            $pack = new PackApplicationWorkflowPresenter();

            try {
                $pack->process($config, $output);
            } catch (\Throwable $e) {
                return $this->fail($output, $e);
            }
        } else {
            $output->writeln(\sprintf(
                ' · Use an existing "<comment>%s</comment>" build',
                $config->pharPathname,
            ));
        }

        $assemblies = AssemblyCollection::createFromBuiltinAssemblies();


        // ---------------------------------------------------------------------
        //  Platforms
        // ---------------------------------------------------------------------
        if (($platforms = $input->getOption('platform')) !== []) {
            $assemblies = $assemblies->withExpectedFamilies(
                families: \array_map(Family::from(...), $platforms),
            );
        }

        $output->writeln(' · Target platforms:');
        foreach ($assemblies->getAvailableFamilies() as $family) {
            $output->writeln('   ↳ <info>' . $family . '</info>');
        }


        // ---------------------------------------------------------------------
        //  Architectures
        // ---------------------------------------------------------------------
        if (($architectures = $input->getOption('arch')) !== []) {
            $assemblies = $assemblies->withExpectedArchitectures(
                architectures: \array_map(Architecture::from(...), $architectures),
            );
        }

        $output->writeln(' · Target architectures:');
        foreach ($assemblies->getAvailableArchitectures() as $architecture) {
            $output->writeln('   ↳ <info>' . $architecture . '</info>');
        }


        // ---------------------------------------------------------------------
        //  Edition
        // ---------------------------------------------------------------------
        $assemblies = $assemblies->withExpectedEdition(
            edition: Edition::from($input->getOption('edition')),
        );

        $output->writeln(' · Target editions: ');
        foreach ($assemblies->getAvailableEditions() as $editions) {
            $output->writeln('   ↳ <info>' . $editions . '</info>');
        }

        if ($assemblies->count() === 0) {
            return $this->fail($output, new \RuntimeException(
                message: 'There are no builds available for the specified'
                    . ' combination of OS family and CPU architecture'
            ));
        }

        $workflow = new CompileApplicationWorkflow();

        $output->writeln(\sprintf(
            ' · Build an application in "<comment>%s</comment>"',
            $config->build,
        ));

        foreach ($workflow->process($config, $assemblies) as $data => $status) {
            switch ($status) {
                case ClearBuildAssemblyDirectoryStatus::ReadyToClean:
                    $output->write(\sprintf(
                        '   [<comment>%s</comment>] Cleanup build directory...',
                        $data,
                    ));
                    break;

                case ClearBuildAssemblyDirectoryStatus::Cleaning:
                    $output->write(\sprintf(
                        "\33[2K\r   ↳ Removing \"<comment>%s</comment>\"",
                        $data,
                    ));
                    break;

                case ClearBuildAssemblyDirectoryStatus::Cleaned:
                    $output->writeln(\sprintf(
                        "\33[2K\r   [<comment>%s</comment>] Build directory is cleaned",
                        $data,
                    ));
                    break;

                case CreateBuildAssemblyDirectoryStatus::ReadyToCreate:
                    $output->write(\sprintf(
                        '   [<comment>%s</comment>] Prepare build directory',
                        $data,
                    ));
                    break;

                case CreateBuildAssemblyDirectoryStatus::Created:
                    $output->writeln(\sprintf(
                        "\33[2K\r   [<comment>%s</comment>] Build directory is available",
                        $data,
                    ));
                    break;

                case CompileStatus::ReadyToCompile:
                    $output->write(\sprintf(
                        '   [<comment>%s</comment>] Compilation...',
                        $data,
                    ));
                    break;

                case CompileStatus::Compiled:
                    $output->writeln(\sprintf(
                        "\33[2K\r   [<comment>%s</comment>] Compiled",
                        $data,
                    ));
                    break;
            }
        }

        return self::SUCCESS;
    }
}
