<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow\PackApplication;

enum BoxPackProcessStatus
{
    case ReadyToPack;
    case Packing;
    case Packed;
}
