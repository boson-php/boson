<?php

declare(strict_types=1);

namespace Boson\Component\WeakType\Tests\WeakClosureTest;

abstract class ParentStub
{
    protected function value(): int
    {
        return 5;
    }
}
