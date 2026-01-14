<?php

declare(strict_types=1);

namespace Boson\Component\WeakType;

/**
 * A wrapper class that maintains a weak reference to the bound object
 * of a closure.
 *
 * This class allows callbacks to be executed even if their originally bound
 * object has been garbage collected. When the object is alive, the callback
 * executes with the object as context. When the object is dead, it falls back
 * to executing the callback without object context (statically), but within
 * the original class scope.
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
     * The class name of the bound object.
     *
     * Used for static callback execution when the object is garbage collected.
     *
     * @var class-string<TThis>
     */
    private string $class;

    /**
     * Weak reference to the bound object of the original closure.
     *
     * @var \WeakReference<TThis>
     */
    private \WeakReference $object;

    /**
     * The original closure wrapped by this {@see WeakClosure} instance.
     */
    private \Closure $callback;

    /**
     * Indicates whether the closure is an internal PHP function.
     */
    private bool $isInternal;

    /**
     * @param TThis $reference The object bound to the original closure
     *
     * @internal Use the {@see WeakClosure::create()} instead
     */
    private function __construct(
        object $reference,
        \Closure $callback,
        \ReflectionFunction $reflection,
    ) {
        $this->object = \WeakReference::create($reference);
        $this->callback = self::unbind($this->object, $callback, $reflection);
        $this->class = self::getClass($reference, $reflection);
        $this->isInternal = $reflection->isInternal();
    }

    /**
     * @param TThis $reference
     *
     * @return class-string
     */
    private static function getClass(object $reference, \ReflectionFunction $reflection): string
    {
        $class = $reflection->getClosureScopeClass();

        if ($class === null) {
            return $reference::class;
        }

        return $class->name;
    }

    /**
     * Unbind `$this` context from a passed closure and creates a new
     * closure that references the target object weakly.
     */
    private static function unbind(object $target, \Closure $callback, \ReflectionFunction $reflection): \Closure
    {
        if ($reflection->isAnonymous()) {
            return $callback->bindTo($target);
        }

        $method = $reflection->getShortName();

        return function (mixed ...$args) use ($method): mixed {
            return $this->{$method}(...$args);
        };
    }

    /**
     * Creates a weak closure from the passed.
     *
     * If the closure is not bound to an object, returns it unchanged.
     *
     * Otherwise, wraps it in a {@see WeakClosure} that maintains a weak
     * reference to the bound object
     *
     * @template TArgClosure of \Closure
     *
     * @param TArgClosure $callback The callable to potentially wrap
     *
     * @return TArgClosure|\Closure Returns a {@see \Closure}
     *         instance with weak reference to `$this`
     *
     * @noinspection PhpDocMissingThrowsInspection An exception never throws
     */
    public static function create(\Closure $callback): \Closure
    {
        $reflection = new \ReflectionFunction($callback);

        $context = $reflection->getClosureThis();

        if ($context === null) {
            return $callback;
        }

        return (new self($context, $callback, $reflection))(...);
    }

    /**
     * Invokes the wrapped callback with the provided arguments
     *
     * If the originally bound object is still alive, the callback is invoked
     * with that object as context. If the object has been garbage collected,
     * the callback is invoked statically within the original class scope
     *
     * @param mixed ...$args Arguments to pass to the callback
     *
     * @return mixed The result of the callback execution
     * @throws \RuntimeException If the GC has freed the bound object
     */
    public function __invoke(mixed ...$args): mixed
    {
        $self = $this->object->get();

        if ($self === null) {
            $shortClassName = $this->class;

            if (\is_int($shortClassNameOffset = \strpos($shortClassName, "\0"))) {
                $shortClassName = \substr($shortClassName, 0, $shortClassNameOffset);
            }

            throw new \RuntimeException(\sprintf(
                'Cannot call a closure, instance of %s has already been removed by the GC',
                $shortClassName,
            ));
        }

        if ($this->isInternal) {
            return $this->callback->bindTo($self)(...$args);
        }

        return $this->callback->call($self, ...$args);
    }
}
