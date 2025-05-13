<?php

declare(strict_types=1);

namespace Boson\Shared\IdValueGenerator;

/**
 * @template-covariant TIntValue of int
 *
 * @template-extends IdValueGeneratorInterface<TIntValue>
 */
interface IntIdValueGeneratorInterface extends IdValueGeneratorInterface
{
    /**
     * Gets initial value of generator.
     */
    public int $initial {
        /**
         * @return TIntValue
         */
        get;
    }

    /**
     * Gets maximum supported generator value.
     */
    public int $maximum {
        /**
         * @return TIntValue
         */
        get;
    }

    /**
     * @return TIntValue
     */
    public function nextId(): int;
}
