<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow\Runtime;

use Boson\Component\Compiler\Workflow\Step\Step;
use Boson\Component\Compiler\Workflow\Task;

/**
 * @template TWorkflow of object
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Component\Compiler\Workflow
 */
final readonly class WorkflowExecutor
{
    private \ReflectionObject $reflection;

    public function __construct(
        /**
         * @var TWorkflow
         */
        private object $context,
    ) {
        $this->reflection = new \ReflectionObject($this->context);
    }

    /**
     * @param non-empty-string $name
     * @param array<array-key, mixed> $arguments
     */
    public function execute(string $name, array $arguments): mixed
    {
        $process = Task::capture(function () use ($name, $arguments) {
            $method = $this->reflection->getMethod($name);

            return $method->invokeArgs($this->context, $arguments);
        });

        foreach ($process as $step) {
            if (\Fiber::getCurrent() !== null) {
                if ($step instanceof Step) {
                    $step = $step->withLevel($step->level + 1);
                }

                \Fiber::suspend($step);
            }
        }

        return $process->getReturn();
    }
}
