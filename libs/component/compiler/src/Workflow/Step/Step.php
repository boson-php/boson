<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow\Step;

use Boson\Component\Compiler\Workflow\Step\Context\ContextInterface;
use Boson\Component\Compiler\Workflow\Step\Context\FunctionContext;
use Boson\Component\Compiler\Workflow\Step\Context\GlobalContext;
use Boson\Component\Compiler\Workflow\Step\Context\MethodContext;
use Boson\Component\Compiler\Workflow\Task;

abstract readonly class Step
{
    public ContextInterface $context;

    public function __construct(
        /**
         * @var int<0, max>
         */
        public int $level = 0,
        ?ContextInterface $context = null,
    ) {
        $this->context = $context ?? $this->getContextFromBacktrace();
    }

    /**
     * @param int<0, max> $level
     */
    abstract public function withLevel(int $level): self;

    private function getContextFromBacktrace(): ContextInterface
    {
        foreach (\debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS) as $trace) {
            // Skip local namespaces
            if (isset($trace['class']) && $this->isNonCapturedClass($trace['class'])) {
                continue;
            }

            return match (true) {
                isset($trace['class']) => new MethodContext(
                    class: $trace['class'],
                    /** @phpstan-ignore-next-line : Method name cannot be empty */
                    method: $trace['function'],
                ),
                /** @phpstan-ignore-next-line : Function name can be not defined in {main} context (?) */
                isset($trace['function']) => new FunctionContext(
                    /** @phpstan-ignore-next-line : Function name cannot be empty */
                    function: $trace['function'],
                ),
                default => new GlobalContext(),
            };
        }

        return new GlobalContext();
    }

    /**
     * @param class-string $class
     */
    private function isNonCapturedClass(string $class): bool
    {
        return \str_starts_with($class, __NAMESPACE__)
            || $class === Task::class;
    }
}
