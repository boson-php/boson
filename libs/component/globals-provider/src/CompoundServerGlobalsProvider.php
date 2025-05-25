<?php

declare(strict_types=1);

namespace Boson\Component\GlobalsProvider;

use Boson\Contracts\Http\RequestInterface;

final readonly class CompoundServerGlobalsProvider implements ServerGlobalsProviderInterface
{
    /**
     * @var list<ServerGlobalsProviderInterface>
     */
    private array $providers;

    /**
     * @param iterable<mixed, ServerGlobalsProviderInterface> $providers
     */
    public function __construct(iterable $providers)
    {
        $this->providers = \iterator_to_array($providers, false);
    }

    public function getServerGlobals(RequestInterface $request): array
    {
        $result = [];

        foreach ($this->providers as $provider) {
            $result = [...$result, ...$provider->getServerGlobals($request)];
        }

        return $result;
    }
}
