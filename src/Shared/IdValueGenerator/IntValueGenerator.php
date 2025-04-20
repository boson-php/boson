<?php

declare(strict_types=1);

namespace Boson\Shared\IdValueGenerator;

use Boson\Shared\IdValueGenerator\Exception\IdOverflowException;

/**
 * @template TInteger of int<min, max>
 * @template-implements IdValueGeneratorInterface<TInteger>
 */
abstract class IntValueGenerator implements IdValueGeneratorInterface
{
    /**
     * @var TInteger
     */
    protected int $current;

    /**
     * Gets initial value of generator
     *
     * @var TInteger
     */
    abstract public int $initial { get; }

    /**
     * Gets maximum supported generator value
     *
     * @var TInteger
     */
    abstract public int $maximum { get; }

    public function __construct(
        private readonly OverflowBehaviour $onOverflow = OverflowBehaviour::Reset,
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
            return new Int64Generator($onOverflow);
        }

        return new Int32Generator($onOverflow);
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
     * @return TInteger
     * @throws IdOverflowException
     */
    public function nextId(): int
    {
        $value = ++$this->current;

        if ($value >= $this->maximum) {
            $this->reset();
        }

        /** @var TInteger */
        return $value;
    }
}
