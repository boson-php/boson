<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Configuration\Factory;

use Boson\Component\Compiler\Configuration;

interface ConfigurationFactoryInterface
{
    public function createConfiguration(Configuration $config): Configuration;
}
