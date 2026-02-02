<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow;

use Boson\Component\Compiler\Configuration;
use Boson\Component\Compiler\Workflow\Runtime\WorkflowProxy;
use Boson\Component\Compiler\Workflow\Step\InfoStep;
use Boson\Component\Compiler\Workflow\Step\NotifyStep;
use Boson\Component\Compiler\Workflow\Step\ProgressStep;
use Boson\Component\Compiler\Workflow\Step\Step;
use Boson\Component\Compiler\Workflow\Task\TaskInterface;

/**
 * @template TWorkflow of object
 */
final readonly class Task
{
    /**
     * @template TArgWorkflow of object
     *
     * @param TArgWorkflow $workflow
     *
     * @return TArgWorkflow
     * @phpstan-return WorkflowProxy<TArgWorkflow>
     */
    public static function new(object $workflow): object
    {
        return new WorkflowProxy($workflow);
    }

    /**
     * @template TArgResult of mixed
     *
     * @param TaskInterface<TArgResult> $task
     *
     * @return TArgResult
     */
    public static function run(Configuration $config, TaskInterface $task): mixed
    {
        return self::new($task)($config);
    }

    /**
     * @template TArgResult of mixed
     *
     * @param callable():TArgResult $context
     *
     * @return \Generator<mixed, Step, Step, TArgResult>
     * @throws \Throwable
     */
    public static function capture(callable $context): \Generator
    {
        /** @var \Fiber<Step, Step, TArgResult, Step> $fiber */
        $fiber = new \Fiber($context);

        return self::toCoroutine($fiber);
    }

    /**
     * @param non-empty-string $message
     * @param array<array-key, scalar> $args
     *
     * @throws \Throwable
     */
    public static function notify(string $message, array $args = []): void
    {
        if (\Fiber::getCurrent() === null) {
            return;
        }

        try {
            \Fiber::suspend(new NotifyStep($message, \array_values($args)));
        } catch (\Throwable) {
            // NO-OP
        }
    }

    /**
     * @param non-empty-string $message
     * @param array<array-key, scalar> $args
     *
     * @throws \Throwable
     */
    public static function progress(string $message, array $args = []): void
    {
        if (\Fiber::getCurrent() === null) {
            return;
        }

        try {
            \Fiber::suspend(new ProgressStep($message, \array_values($args)));
        } catch (\Throwable) {
            // NO-OP
        }
    }

    /**
     * @param non-empty-string $message
     * @param array<array-key, scalar> $args
     */
    public static function info(string $message, array $args = []): void
    {
        if (\Fiber::getCurrent() === null) {
            return;
        }

        try {
            \Fiber::suspend(new InfoStep($message, \array_values($args)));
        } catch (\Throwable) {
            // NO-OP
        }
    }

    /**
     * @template TArgStep of Step|null
     * @template TArgResult of mixed
     *
     * @param \Fiber<TArgStep, TArgStep, TArgResult, TArgStep> $fiber
     *
     * @return \Generator<array-key, TArgStep, TArgStep, TArgResult>
     * @throws \Throwable
     */
    private static function toCoroutine(\Fiber $fiber): \Generator
    {
        $value = null;

        if ($fiber->isTerminated()) {
            return $fiber->getReturn();
        }

        if (!$fiber->isStarted()) {
            /** @phpstan-ignore generator.valueType */
            $value = yield $fiber->start();
        }

        /** @phpstan-ignore booleanNot.alwaysTrue */
        while (!$fiber->isTerminated()) {
            /** @phpstan-ignore argument.type */
            $value = $fiber->resume($value);

            // The last call to "resume()" moves the execution of the
            // Fiber to the "return" stmt.
            //
            // So the "yield" is not needed. Skip this step and return
            // the result.
            /** @phpstan-ignore if.alwaysFalse */
            if ($fiber->isTerminated()) {
                break;
            }

            /** @phpstan-ignore generator.valueType */
            $value = yield $value;
        }

        /** @phpstan-ignore deadCode.unreachable */
        return $fiber->getReturn();
    }
}
