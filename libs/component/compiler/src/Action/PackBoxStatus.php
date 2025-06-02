<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Action;

enum PackBoxStatus
{
    case ReadyToPack;
    case Packing;
    case Packed;
}
