<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow;

use Boson\Component\Compiler\Action\ClearBuildAssemblyDirectoryAction;
use Boson\Component\Compiler\Action\CompileAction;
use Boson\Component\Compiler\Action\CreateBuildAssemblyDirectoryAction;
use Boson\Component\Compiler\Assembly\Assembly;
use Boson\Component\Compiler\Configuration;

final readonly class CompileApplicationWorkflow
{
    /**
     * @param iterable<mixed, Assembly> $assemblies
     * @return iterable<mixed, \UnitEnum>
     */
    public function process(Configuration $config, iterable $assemblies): iterable
    {
        foreach ($assemblies as $assembly) {
            // Clear build directory
            yield from new ClearBuildAssemblyDirectoryAction($assembly)
                ->process($config);

            // Create build directory
            yield from new CreateBuildAssemblyDirectoryAction($assembly)
                ->process($config);

            // Compile assembly
            yield from new CompileAction($assembly)
                ->process($config);
        }
    }
}
