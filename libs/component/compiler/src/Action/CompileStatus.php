<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Action;

enum CompileStatus
{
    case ReadyToCompile;
    case Compiled;
}
