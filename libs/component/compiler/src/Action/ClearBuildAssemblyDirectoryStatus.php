<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Action;

enum ClearBuildAssemblyDirectoryStatus
{
    case ReadyToClean;
    case Cleaning;
    case Cleaned;
}
