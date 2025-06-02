<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Action;

enum CreateBuildDirectoryStatus
{
    case ReadyToCreate;
    case Created;
}
