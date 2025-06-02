<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Action;

enum CreateBuildAssemblyDirectoryStatus
{
    case ReadyToCreate;
    case Created;
}
