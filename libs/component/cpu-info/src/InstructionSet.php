<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo;

use Boson\Component\CpuInfo\InstructionSet\BuiltinInstructionSet;
use Boson\Component\CpuInfo\InstructionSet\InstructionSetImpl;

require_once __DIR__ . '/InstructionSet/constants.php';

/**
 * Main class representing CPU instruction sets.
 *
 * Provides a set of predefined CPU instruction sets and methods to work
 * with them. It supports both built-in instruction sets (like MMX, SSE,
 * AVX, etc.) and custom instruction sets.
 */
final class InstructionSet implements InstructionSetInterface
{
    use InstructionSetImpl;

    /**
     * MultiMedia eXtensions instruction set
     */
    public const InstructionSetInterface MMX = InstructionSet\MMX;

    /**
     * Streaming SIMD Extensions instruction set
     */
    public const InstructionSetInterface SSE = InstructionSet\SSE;

    /**
     * Streaming SIMD Extensions 2 instruction set
     */
    public const InstructionSetInterface SSE2 = InstructionSet\SSE2;

    /**
     * Streaming SIMD Extensions 3 instruction set
     */
    public const InstructionSetInterface SSE3 = InstructionSet\SSE3;

    /**
     * Supplemental Streaming SIMD Extensions 3 instruction set
     */
    public const InstructionSetInterface SSSE3 = InstructionSet\SSSE3;

    /**
     * Streaming SIMD Extensions 4.1 instruction set
     */
    public const InstructionSetInterface SSE4_1 = InstructionSet\SSE4_1;

    /**
     * Streaming SIMD Extensions 4.2 instruction set
     */
    public const InstructionSetInterface SSE4_2 = InstructionSet\SSE4_2;

    /**
     * Fused Multiply-Add 3 instruction set
     */
    public const InstructionSetInterface FMA3 = InstructionSet\FMA3;

    /**
     * Advanced Vector Extensions instruction set
     */
    public const InstructionSetInterface AVX = InstructionSet\AVX;

    /**
     * Advanced Vector Extensions 2 instruction set
     */
    public const InstructionSetInterface AVX2 = InstructionSet\AVX2;

    /**
     * Advanced Vector Extensions 512 instruction set
     */
    public const InstructionSetInterface AVX512 = InstructionSet\AVX512;

    /**
     * Attempts to create a built-in instruction set instance from a name.
     *
     * This method tries to match the given name against known instruction set
     * names and their aliases. For example, 'pni' will match the SSE3
     * instruction set.
     *
     * @api
     */
    public static function tryFrom(string $name): ?BuiltinInstructionSet
    {
        return BuiltinInstructionSet::tryFrom($name);
    }

    /**
     * Creates an instruction set instance from a name.
     *
     * This method first attempts to find a matching built-in instruction set.
     * If no match is found, it creates a new custom instruction set with the
     * given name.
     *
     * @api
     */
    public static function from(string $name): InstructionSetInterface
    {
        return self::tryFrom($name) ?? new self($name);
    }

    /**
     * Returns a list of all built-in instruction sets.
     *
     * This method returns all the predefined instruction set constants
     * defined in this class.
     *
     * @api
     *
     * @return non-empty-list<InstructionSetInterface>
     */
    public static function cases(): array
    {
        /** @var non-empty-array<non-empty-string, ArchitectureInterface> $cases */
        static $cases = new \ReflectionClass(self::class)
            ->getConstants();

        /** @var non-empty-list<ArchitectureInterface> */
        return \array_values($cases);
    }
}
