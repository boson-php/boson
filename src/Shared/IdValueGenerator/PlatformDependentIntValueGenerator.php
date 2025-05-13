<?php

declare(strict_types=1);

namespace Boson\Shared\IdValueGenerator;

/**
 * @template-implements IntIdValueGeneratorInterface<int>
 */
final readonly class PlatformDependentIntValueGenerator implements IntIdValueGeneratorInterface
{
    /**
     * @var IntIdValueGeneratorInterface<int>
     */
    private IntIdValueGeneratorInterface $generator;

    public int $initial;
    public int $maximum;

    public function __construct(OverflowBehaviour $onOverflow = OverflowBehaviour::DEFAULT)
    {
        $this->generator = $this->createFromPlatform($onOverflow);

        $this->initial = $this->generator->initial;
        $this->maximum = $this->generator->maximum;
    }

    /**
     * @return IntIdValueGeneratorInterface<int>
     */
    private function createFromPlatform(OverflowBehaviour $onOverflow): IntIdValueGeneratorInterface
    {
        if (\PHP_INT_SIZE >= 8) {
            return new Int64ValueGenerator($onOverflow);
        }

        return new Int32ValueGenerator($onOverflow);
    }

    public function nextId(): int
    {
        return $this->generator->nextId();
    }
}
