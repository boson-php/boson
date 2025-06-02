<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow;

use Boson\Component\Compiler\Assembly\Assembly;
use Boson\Component\Compiler\Configuration;
use Boson\Component\Compiler\Workflow\CompileApplication\CompileApplicationProcess;
use Boson\Component\Compiler\Workflow\CompileApplication\PrepareProcess;

final readonly class CompileApplicationWorkflow
{
    private PrepareProcess $prepare;
    private CompileApplicationProcess $compile;

    public function __construct()
    {
        $this->prepare = new PrepareProcess();
        $this->compile = new CompileApplicationProcess();
    }

    /**
     * @param iterable<mixed, Assembly> $assemblies
     * @return iterable<mixed, \UnitEnum>
     */
    public function process(Configuration $configuration, iterable $assemblies): iterable
    {
        foreach ($assemblies as $assembly) {
            yield from $this->prepare->process($configuration, $assembly);
            yield from $this->compile->process($configuration, $assembly);
        }
    }
}
