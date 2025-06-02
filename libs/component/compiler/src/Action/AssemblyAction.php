<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Action;

use Boson\Component\Compiler\Assembly\Assembly;

/**
 * @template TStatus of \UnitEnum
 *
 * @template-implements ActionInterface<TStatus>
 */
abstract readonly class AssemblyAction implements ActionInterface
{
    public function __construct(
        protected Assembly $assembly,
    ) {}
}
