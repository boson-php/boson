<?php

declare(strict_types=1);

namespace Boson\Dispatcher;

use Boson\Dispatcher\Subscription\CancellableSubscriptionInterface;

/**
 * @phpstan-require-implements EventListenerProviderInterface
 * @mixin EventListenerProviderInterface
 */
trait EventListenerProvider
{
    public function on(\Closure|string $eventOrListener, ?\Closure $listener = null): CancellableSubscriptionInterface
    {
        if ($eventOrListener instanceof \Closure) {
            return $this->addEventListenerByCallback($eventOrListener);
        }

        if ($listener === null) {
            throw new \InvalidArgumentException('Second parameter must be a listener callback');
        }

        return $this->events->addEventListener($eventOrListener, $listener);
    }

    /**
     * @template TArgEvent of object
     *
     * @param \Closure(TArgEvent):void $listener
     *
     * @return CancellableSubscriptionInterface<TArgEvent>
     */
    private function addEventListenerByCallback(\Closure $listener): CancellableSubscriptionInterface
    {
        try {
            $parameters = new \ReflectionFunction($listener)
                ->getParameters();
        } catch (\ReflectionException $e) {
            throw new \InvalidArgumentException('Could not parse event listener', previous: $e);
        }

        foreach ($parameters as $parameter) {
            /** @var class-string<TArgEvent> $type */
            $type = $this->getParameterTypeName($parameter);

            return $this->events->addEventListener($type, $listener);
        }

        throw new \InvalidArgumentException(
            message: 'The event subscriber must have at least one (event instance) parameter',
        );
    }

    /**
     * @return non-empty-string
     */
    private function getParameterTypeName(\ReflectionParameter $parameter): string
    {
        $type = $parameter->getType();

        if ($type instanceof \ReflectionNamedType) {
            /** @var non-empty-string */
            return $type->getName();
        }

        throw new \InvalidArgumentException(\sprintf(
            'Argument #%d ($%s) of event listener must contain listened event type-hint',
            $parameter->getPosition(),
            $parameter->getName(),
        ));
    }
}
