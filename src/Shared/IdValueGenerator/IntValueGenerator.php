<?php

declare(strict_types=1);

namespace Boson\Shared\IdValueGenerator;

use Boson\Shared\IdValueGenerator\Exception\IdOverflowException;

/**
 * @template TIntValue of int
 * @template-implements IntIdValueGeneratorInterface<TIntValue>
 */
abstract class IntValueGenerator implements IntIdValueGeneratorInterface
{
    /**
     * @var TIntValue
     */
    protected int $current;

    /**
     * Gets initial value of generator
     *
     * @var TIntValue
     */
    abstract public int $initial { get; }

    /**
     * Gets maximum supported generator value
     *
     * @var TIntValue
     */
    abstract public int $maximum { get; }

    public function __construct(
        private readonly OverflowBehaviour $onOverflow = OverflowBehaviour::DEFAULT,
    ) {
        $this->current = $this->initial;
    }

    /**
     * @return IdValueGeneratorInterface<array-key>
     */
    public static function createFromEnvironment(
        OverflowBehaviour $onOverflow = OverflowBehaviour::Reset,
    ): IdValueGeneratorInterface {
        if (\PHP_INT_SIZE >= 8) {
            return new Int64ValueGenerator($onOverflow);
        }

        return new Int32ValueGenerator($onOverflow);
    }

    /**
     * @throws IdOverflowException
     */
    protected function reset(): void
    {
        if ($this->onOverflow === OverflowBehaviour::Exception) {
            throw IdOverflowException::becauseClassOverflows(static::class, (string) $this->current);
        }

        $this->current = $this->initial;
    }

    /**
     * @return TIntValue
     * @throws IdOverflowException
     */
    public function nextId(): int
    {
        $value = ++$this->current;

        if ($value >= $this->maximum) {
            $this->reset();
        }

        /** @var TIntValue */
        return $value;
    }
}
