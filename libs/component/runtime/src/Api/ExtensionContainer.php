<?php

declare(strict_types=1);

namespace Boson\Api;

use Boson\Dispatcher\EventDispatcherInterface;
use Boson\Dispatcher\EventListenerInterface;
use Boson\Internal\Saucer\LibSaucer;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @template T of object
 */
abstract class ExtensionContainer implements ContainerInterface
{
    /**
     * @var array<non-empty-string, class-string<Extension<T>>>
     */
    private array $extensions = [];

    /**
     * @var array<non-empty-string, Extension<T>>
     */
    private array $instances = [];

    public function __construct(
        private readonly LibSaucer $api,
        /**
         * @var T
         */
        private readonly object $context,
        private readonly EventListenerInterface $listener,
        private readonly EventDispatcherInterface $dispatcher,
    ) {}

    /**
     * @param non-empty-string $id
     * @param class-string<Extension<T>> $class
     */
    public function register(string $id, string $class): void
    {
        $this->extensions[$id] = $class;
    }

    /**
     * @param non-empty-string $id
     * @return Extension<T>
     */
    public function get(string $id): Extension
    {
        return $this->instances[$id] ??= $this->create($id);
    }

    /**
     * @param non-empty-string $id
     * @return Extension<T>
     */
    private function create(string $id): Extension
    {
        $extension = $this->extensions[$id] ?? throw new class(\sprintf('Service [%s] not found', $id))
            extends \InvalidArgumentException
            implements NotFoundExceptionInterface {};

        return new $extension($this->api, $this->context, $this->listener, $this->dispatcher);
    }

    /**
     * @param non-empty-string $id
     */
    public function has(string $id): bool
    {
        return isset($this->extensions[$id]);
    }
}
