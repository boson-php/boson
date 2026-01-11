<?php

declare(strict_types=1);

namespace Boson\Extension;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Exception\ExtensionAlreadyLoadedException;
use Boson\Extension\Exception\ExtensionLoadingException;
use Boson\Extension\Exception\ExtensionNotFoundException;
use Boson\Extension\Loader\DependencyGraph;
use Internal\Destroy\Destroyable;
use Psr\Container\ContainerInterface;

/**
 * @template TContext of IdentifiableInterface
 */
final class Registry implements ContainerInterface, Destroyable
{
    /**
     * @var list<object>
     *
     * @phpstan-ignore-next-line : Just keep extensions list in memory
     */
    private array $privateExtensions = [];

    /**
     * @var array<non-empty-string, object>
     */
    private array $publicExtensions = [];

    /**
     * @var list<ExtensionInterface<TContext>>
     */
    private array $providers = [];

    private bool $booted = false;

    /**
     * @param iterable<array-key, ExtensionInterface<TContext>> $providers
     *
     * @throws ExtensionLoadingException
     */
    public function __construct(
        private readonly EventListener $listener,
        iterable $providers = [],
    ) {
        $this->providers = \iterator_to_array($providers, false);
    }

    /**
     * @param TContext $context
     * @return array<non-empty-string, object>
     * @throws ExtensionLoadingException
     */
    public function boot(IdentifiableInterface $context): array
    {
        if ($this->booted === true) {
            return $this->publicExtensions;
        }

        /** @var ExtensionInterface<TContext> $provider */
        foreach (new DependencyGraph($this->providers) as $provider) {
            try {
                $extension = $provider->load($context, $this->listener);
            } catch (\Throwable $e) {
                throw ExtensionLoadingException::becauseLoadingExceptionOccurs($e);
            }

            // Skip in case of extension will not load
            if ($extension === null) {
                continue;
            }

            // Load as public extension
            foreach ($provider->aliases as $alias) {
                if (isset($this->publicExtensions[$alias])) {
                    throw ExtensionAlreadyLoadedException::becauseExtensionAlreadyLoaded($alias);
                }

                $this->publicExtensions[$alias] = $extension;
            }

            // Otherwise load as private extension
            if ($provider->aliases === []) {
                $this->privateExtensions[] = $extension;
            }
        }

        $this->providers = [];
        $this->booted = true;

        return $this->publicExtensions;
    }

    /**
     * @template TArgService of object
     *
     * @param class-string<TArgService>|string $id
     *
     * @return ($id is class-string<TArgService> ? TArgService : object)
     * @throws ExtensionNotFoundException
     */
    public function get(string $id): object
    {
        return $this->publicExtensions[$id]
            ?? throw ExtensionNotFoundException::becauseExtensionNotFound($id);
    }

    public function has(string $id): bool
    {
        return isset($this->publicExtensions[$id]);
    }

    /**
     * @internal for internal usage only
     */
    public function destroy(): void
    {
        $destroyed = new \SplObjectStorage();

        foreach ($this->privateExtensions as $extension) {
            if ($extension instanceof Destroyable) {
                $destroyed->offsetSet($extension);
            }
        }

        foreach ($this->publicExtensions as $extension) {
            if ($extension instanceof Destroyable) {
                $destroyed->offsetSet($extension);
            }
        }

        $this->privateExtensions = [];
        $this->publicExtensions = [];

        foreach ($destroyed as $extension) {
            $extension->destroy();
        }

        unset($destroyed);

        \gc_collect_cycles();
    }

    public function __destruct()
    {
        $this->destroy();
    }
}
