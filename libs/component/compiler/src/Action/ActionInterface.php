<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Action;

use Boson\Component\Compiler\Configuration;

/**
 * @template TStatus of \UnitEnum
 */
interface ActionInterface
{
    /**
     * @return iterable<mixed, TStatus>
     */
    public function process(Configuration $config): iterable;
}
