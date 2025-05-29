<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\InstructionSet\Factory\CpuId;

interface CpuIdExecutorInterface
{
    public function execute(\Stringable|string $expr): bool;
}
