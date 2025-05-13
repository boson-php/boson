<?php

declare(strict_types=1);

namespace Boson\Shared\IdValueGenerator;

/**
 * @template TIntValue of int
 *
 * @template-extends IdValueGeneratorInterface<TIntValue>
 */
interface IntIdValueGeneratorInterface extends IdValueGeneratorInterface
{
    /**
     * Gets initial value of generator.
     *
     * @var TIntValue
     */
    public int $initial { get; }

    /**
     * Gets maximum supported generator value.
     *
     * @var TIntValue
     */
    public int $maximum { get; }

    /**
     * @return TIntValue
     */
    public function nextId(): int;
}
