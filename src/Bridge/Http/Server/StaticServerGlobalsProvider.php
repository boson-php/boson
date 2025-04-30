<?php

declare(strict_types=1);

namespace Boson\Bridge\Http\Server;

use Boson\Http\RequestInterface;

/**
 * Returns constant server parameters that are independent of the request.
 */
final readonly class StaticServerGlobalsProvider implements ServerGlobalsProviderInterface
{
    /**
     * @var array<non-empty-string, scalar>
     */
    private array $server;

    /**
     * @param array<array-key, mixed>|null $default
     */
    public function __construct(?array $default = null)
    {
        $this->server = $this->extendServerGlobalParams(
            server: $this->filterServerGlobalParams($default ?? $_SERVER),
        );
    }

    /**
     * @param array<non-empty-string, scalar> $server
     *
     * @return array<non-empty-string, scalar>
     */
    private function extendServerGlobalParams(array $server): array
    {
        // Normalize document root in case of document root is empty or undefined
        if (($server['DOCUMENT_ROOT'] ?? '') === '') {
            $server['DOCUMENT_ROOT'] = match (true) {
                isset($server['SCRIPT_FILENAME']) && \is_string($server['SCRIPT_FILENAME'])
                => \dirname($server['SCRIPT_FILENAME']),
                default => (string) \getcwd(),
            };
        }

        $server['SERVER_NAME'] ??= '0.0.0.0';
        $server['SERVER_PORT'] ??= '0';
        $server['SERVER_SOFTWARE'] ??= 'Boson Runtime';

        return $server;
    }

    /**
     * @param array<array-key, mixed> $server
     *
     * @return array<non-empty-string, scalar>
     */
    private function filterServerGlobalParams(array $server): array
    {
        $result = [];

        foreach ($server as $key => $value) {
            if (\is_string($key) && $key !== '' && \is_scalar($value)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public function getServerGlobals(RequestInterface $request): array
    {
        return $this->server;
    }
}
