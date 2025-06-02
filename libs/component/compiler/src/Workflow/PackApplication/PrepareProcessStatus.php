<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow\PackApplication;

enum PrepareProcessStatus
{
    case ReadyToCreateBuildDirectory;
    case CreatedBuildDirectory;

    case ReadyToCreateBoxConfig;
    case CreatedBoxConfig;
}
