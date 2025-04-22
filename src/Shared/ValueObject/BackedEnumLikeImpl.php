<?php

declare(strict_types=1);

namespace Boson\Shared\ValueObject;

/**
 * Emulates native enums when it is impossible due to the physical
 * limitations of the language.
 *
 * @template T of object
 * @template TValue of string|int
 *
 * @link https://externals.io/message/118040
 * @link https://externals.io/message/119757
 * @link https://externals.io/message/120121
 * @link https://externals.io/message/126332
 * @link https://externals.io/message/123388
 * @link https://externals.io/message/124991
 * @link https://externals.io/message/126522
 */
trait BackedEnumLikeImpl
{
    /** @use UnitEnumLikeImpl<T> */
    use UnitEnumLikeImpl;

    //
    // Abstract properties not available.
    // @link https://github.com/php/php-src/issues/18391
    //
    // abstract public string|int $value { get; }
    //

    /**
     * Translates a {@see string} or {@see int} into the corresponding
     * enum-like class case (constant), if any.
     *
     * If there is no matching case defined, it will throw a {@see \ValueError}.
     *
     * Note: Emulates {@see \BackedEnum::from()} method definition.
     *
     * @param TValue $value
     *
     * @return T
     * @throws \ValueError if there is no matching case defined
     */
    public static function from(string|int $value): object
    {
        return self::tryFrom($value)
            ?? throw new \ValueError(\sprintf(
                '"%s" is not a valid value for %s',
                \var_export($value, true),
                static::class,
            ));
    }

    /**
     * @param T $case
     *
     * @return TValue
     */
    abstract protected static function caseKeyFor(object $case): string|int;

    /**
     * Translates a {@see string} or {@see int} into the corresponding
     * enum-like class case (constant), if any.
     *
     * If there is no matching case defined, it will return {@see null}.
     *
     * Note: Emulates {@see \BackedEnum::tryFrom()} method definition.
     *
     * @param TValue $value
     *
     * @return T|null
     */
    public static function tryFrom(string|int $value): ?object
    {
        /**
         * List of memoized constants.
         *
         * @var array<TValue, T> $cases
         */
        static $cases = [];

        if ($cases === []) {
            foreach (self::getDefinedConstants() as $constant) {
                $cases[self::caseKeyFor($constant)] = $constant;
            }
        }

        return $cases[$value] ?? null;
    }
}
