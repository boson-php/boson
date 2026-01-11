<?php

declare(strict_types=1);

namespace Boson\Component\WeakType;

/**
 * A wrapper class that maintains a weak reference to the bound object of a closure
 *
 * This class allows callbacks to be executed even if their originally bound
 * object has been garbage collected. When the object is alive, the callback
 * executes with the object as context. When the object is dead, it falls back
 * to executing the callback without object context (statically), but within
 * the original class scope
 *
 * ```
 * $closure = WeakClosure::create(function() {
 *     // The pointer to the `$this` is optional
 *     var_dump($this);
 * });
 *
 * $closure();
 * ```
 *
 * @template TThis of object
 */
final readonly class WeakClosure
{
    /**
     * Weak reference to the bound object of the original closure
     *
     * @var \WeakReference<TThis>
     */
    private \WeakReference $reference;

    /**
     * The class name of the bound object
     *
     * Used for static callback execution when the object is garbage collected
     *
     * @var class-string<TThis>
     */
    private string $class;

    /**
     * The original closure wrapped by this {@see WeakClosure} instance
     */
    private \Closure $callback;

    /**
     * @internal Use the {@see WeakClosure::create()} instead
     *
     * @param TThis $reference The object bound to the original closure
     * @param \Closure $callback The original closure to wrap
     */
    private function __construct(object $reference, \Closure $callback)
    {
        $this->reference = \WeakReference::create($reference);
        $this->callback = $callback->bindTo($this);
        $this->class = $reference::class;
    }

    /**
     * Creates a {@see WeakClosure} from a callable, or returns the callable
     * unchanged if it is not bound to an object
     *
     * @param callable $callback The callable to potentially wrap
     * @return callable Returns a {@see WeakClosure} instance if the callable
     *         is bound to an object, otherwise returns the original callable
     * @throws \ReflectionException in case of internal error occurs
     */
    public static function create(callable $callback): callable
    {
        $reference = new \ReflectionFunction($closure = $callback(...))
            ->getClosureThis();

        if ($reference === null) {
            return $closure;
        }

        return new self($reference, $closure);
    }

    /**
     * Invokes the wrapped callback with the provided arguments
     *
     * If the originally bound object is still alive, the callback is invoked
     * with that object as context. If the object has been garbage collected,
     * the callback is invoked statically within the original class scope
     *
     * @param mixed ...$args Arguments to pass to the callback
     * @return mixed The result of the callback execution
     */
    public function __invoke(mixed ...$args): mixed
    {
        $self = $this->reference->get();

        if ($self === null) {
            $callback = $this->callback->bindTo(null, $this->class);

            return $callback(...$args);
        }

        return $this->callback->call($self, ...$args);
    }
}