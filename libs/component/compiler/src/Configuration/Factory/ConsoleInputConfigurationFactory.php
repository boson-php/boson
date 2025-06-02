<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Configuration\Factory;

use Boson\Component\Compiler\Configuration;
use Symfony\Component\Console\Input\InputInterface;

final readonly class ConsoleInputConfigurationFactory implements ConfigurationFactoryInterface
{
    public function __construct(
        private InputInterface $input,
    ) {}

    /**
     * @param non-empty-string $name
     * @return non-empty-string|null
     */
    private function getOptionValue(string $name): ?string
    {
        $input = $this->input;

        if ($input->hasOption($name) && ($value = $input->getOption($name)) !== '') {
            return $value;
        }

        return null;
    }

    public function createConfiguration(Configuration $config): Configuration
    {
        if (($app = $this->getOptionValue('app')) !== null) {
            $config = $config->withName($app);
        }

        if (($entry = $this->getOptionValue('entry')) !== null) {
            $config = $config->withEntrypoint($entry);
        }

        if (($boxVersion = $this->getOptionValue('box-version')) !== null) {
            $config = $config->withBoxVersion($boxVersion);
        }

        if (($temp = $this->getOptionValue('build')) !== null) {
            $config = $config->withBuildDirectory($temp);
        }

        return $config;
    }
}
