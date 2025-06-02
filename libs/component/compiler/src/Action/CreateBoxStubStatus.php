<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Action;

enum CreateBoxStubStatus
{
    case ReadyToCreate;
    case Created;
}
