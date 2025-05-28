<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\InstructionSet;

use Boson\Component\CpuInfo\InstructionSetInterface;

final readonly class BuiltinInstructionSet implements InstructionSetInterface
{
    use InstructionSetImpl;
}
