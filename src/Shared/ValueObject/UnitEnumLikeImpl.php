<?php

declare(strict_types=1);

namespace Boson\Shared\ValueObject;

/**
 * Emulates native enums when it is impossible due to the physical
 * limitations of the language.
 *
 * @template T of object
 *
 * @link https://externals.io/message/118040
 * @link https://externals.io/message/119757
 * @link https://externals.io/message/120121
 * @link https://externals.io/message/126332
 * @link https://externals.io/message/123388
 * @link https://externals.io/message/124991
 * @link https://externals.io/message/126522
 */
trait UnitEnumLikeImpl
{
    //
    // Abstract properties not available.
    // @link https://github.com/php/php-src/issues/18391
    //
    // abstract public string $name { get; }
    //

    /**
     * @return array<non-empty-string, T>
     */
    private static function getDefinedConstants(): array
    {
        /**
         * List of memoized builtin cases.
         *
         * @var array<non-empty-string, T> $cases
         */
        static $cases = new \ReflectionClass(self::class)
            ->getConstants(\ReflectionClassConstant::IS_PUBLIC);

        return $cases;
    }

    /**
     * Returns list of cases (constants) on this enum-like class.
     *
     * Note: Emulates {@see \UnitEnum::cases()} method definition.
     *
     * @return list<T>
     */
    public static function cases(): array
    {
        return \array_values(self::getDefinedConstants());
    }
}
