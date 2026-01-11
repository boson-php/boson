<?php

declare(strict_types=1);

namespace Boson\Internal\Poller;

use Boson\ApplicationId;
use Boson\Component\Saucer\SaucerInterface;
use Boson\Poller\PollerInterface;
use Boson\Poller\Suspension;
use Boson\Shared\IdValueGenerator\IdValueGeneratorInterface;
use Boson\Shared\IdValueGenerator\PlatformDependentIntValueGenerator;
use FFI\CData;

/**
 * @api
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson
 */
final class SaucerPoller implements PollerInterface
{
    /**
     * @var array<array-key, \Closure(array-key):void>
     */
    private array $microTasks = [];

    /**
     * @var array<array-key, \Closure(array-key):void>
     */
    private array $periodicMicroTasks = [];

    private TaskType $type = TaskType::DEFAULT;

    /**
     * @var IdValueGeneratorInterface<array-key>
     */
    private readonly IdValueGeneratorInterface $ids;

    private readonly CData $ptr;

    public function __construct(
        ApplicationId $id,
        private readonly SaucerInterface $saucer,
    ) {
        $this->ids = $this->createIdValueGenerator();
        $this->ptr = $this->saucer->saucer_loop_new($id->ptr);
    }

    /**
     * @return IdValueGeneratorInterface<array-key>
     */
    private function createIdValueGenerator(): IdValueGeneratorInterface
    {
        return new PlatformDependentIntValueGenerator();
    }

    public function createSuspension(): Suspension
    {
        return new Suspension($this);
    }

    public function throw(\Throwable $e): int|string
    {
        return $this->defer(static fn(): never => throw $e);
    }

    public function next(): void
    {
        switch ($this->type) {
            case TaskType::Internal:
                $this->executeInternalTask();
                break;

            case TaskType::Queued:
                $this->executeQueuedTask();
                break;

            case TaskType::Periodic:
                $this->executePeriodicTask();
                break;
        }

        // Reduces CPU usage
        \usleep(1);

        $this->type = $this->type->next();
    }

    private function executeInternalTask(): void
    {
        $this->saucer->saucer_loop_iteration($this->ptr);
    }

    private function executePeriodicTask(): void
    {
        foreach ($this->periodicMicroTasks as $id => $task) {
            $task($id);

            return;
        }
    }

    private function executeQueuedTask(): void
    {
        foreach ($this->microTasks as $id => $task) {
            unset($this->microTasks[$id]);

            $task($id);

            return;
        }
    }

    public function defer(callable $task): int|string
    {
        $this->microTasks[$id = $this->ids->nextId()] = $task(...);

        return $id;
    }

    public function repeat(callable $task): int|string
    {
        $this->periodicMicroTasks[$id = $this->ids->nextId()] = $task(...);

        return $id;
    }

    public function delay(float $delay, callable $task): int|string
    {
        $stopsAfter = \microtime(true) + $delay;

        return $this->repeat(function (string|int $taskId) use ($stopsAfter, $task): void {
            if (\microtime(true) > $stopsAfter) {
                $task($taskId);

                $this->cancel($taskId);
            }
        });
    }

    public function timer(float $interval, callable $task): int|string
    {
        $execAfter = \microtime(true) + $interval;

        return $this->repeat(function (string|int $taskId) use (&$execAfter, $interval, $task): void {
            if (\microtime(true) > $execAfter) {
                $task($taskId);

                $execAfter += $interval;
            }
        });
    }

    public function cancel(int|string $taskId): void
    {
        unset($this->periodicMicroTasks[$taskId], $this->microTasks[$taskId]);
    }

    public function __destruct()
    {
        $this->saucer->saucer_loop_free($this->ptr);
    }
}
