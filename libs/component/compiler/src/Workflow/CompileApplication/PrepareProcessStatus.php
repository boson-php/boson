<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow\CompileApplication;

enum PrepareProcessStatus
{
    case ReadyToCleanBuildDirectory;
    case CleaningBuildDirectory;
    case CleanedBuildDirectory;

    case ReadyToCreateBuildDirectory;
    case CreatedBuildDirectory;
}
