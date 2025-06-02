<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Action;

enum CreateBoxConfigStatus
{
    case ReadyToCreate;
    case Created;
}
