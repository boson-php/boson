<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Action;

enum CopyRuntimeBinaryStatus
{
    case ReadyToCopy;
    case Copied;
}
