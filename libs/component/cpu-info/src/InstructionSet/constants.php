<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\InstructionSet;

const MMX = new BuiltinInstructionSet('mmx');
const SSE = new BuiltinInstructionSet('sse');
const SSE2 = new BuiltinInstructionSet('sse2');
const SSE3 = new BuiltinInstructionSet('sse3');
const SSSE3 = new BuiltinInstructionSet('ssse3');
const SSE4_1 = new BuiltinInstructionSet('sse4.1');
const SSE4_2 = new BuiltinInstructionSet('sse4.2');
const FMA3 = new BuiltinInstructionSet('fma3');
const AVX = new BuiltinInstructionSet('avx');
const AVX2 = new BuiltinInstructionSet('avx2');
const AVX512 = new BuiltinInstructionSet('avx512');
