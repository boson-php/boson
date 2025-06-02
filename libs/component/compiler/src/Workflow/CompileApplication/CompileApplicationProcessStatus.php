<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow\CompileApplication;

enum CompileApplicationProcessStatus
{
    case CompilationStarting;
    case CompilationCompleted;
}
