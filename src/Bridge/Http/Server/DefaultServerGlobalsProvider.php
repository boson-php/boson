<?php

declare(strict_types=1);

namespace Boson\Bridge\Http\Server;

use Boson\Http\RequestInterface;
use Psr\Clock\ClockInterface;

/**
 * Returns basic request-aware parameters.
 */
final readonly class DefaultServerGlobalsProvider implements ServerGlobalsProviderInterface
{
    /**
     * @var non-empty-uppercase-string
     */
    private const string UPPER = '_ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * @var non-empty-lowercase-string
     */
    private const string LOWER = '-abcdefghijklmnopqrstuvwxyz';

    private ServerGlobalsProviderInterface $delegate;

    public function __construct(
        ?ServerGlobalsProviderInterface $delegate = new DefaultServerGlobalsProvider(),
        /**
         * Allows you to set time-dependent parameters for server global values.
         *
         * If the value is not set (defined as {@see null}), the system
         * time will be used.
         */
        private ?ClockInterface $clock = null,
    ) {
        $this->delegate = $delegate ?? new EmptyServerGlobalsProvider();
    }

    public function getServerGlobals(RequestInterface $request): array
    {
        return [
            ...$this->delegate->getServerGlobals($request),
            ...$this->getRequestTime(),
            ...$this->getRequestInfo($request),
            ...$this->getRequestHeaders($request),
        ];
    }

    /**
     * @return array<non-empty-uppercase-string, string|int>
     */
    private function getRequestInfo(RequestInterface $request): array
    {
        $segments = \parse_url($request->url);

        if (!\is_array($segments)) {
            $segments = ['path' => '/'];
        }

        return [
            'REQUEST_METHOD' => $request->method,
            'QUERY_STRING' => $query = $segments['query'] ?? '',
            'PATH_INFO' => $path = $segments['path'] ?? '/',
            'REMOTE_ADDR' => $host = $segments['host'] ?? '127.0.0.1',
            'REMOTE_PORT' => $port = $segments['port'] ?? 80,
            // compound parameters
            'REQUEST_URI' => $path . ($query === '' ? '' : '/' . $query),
            'HTTP_HOST' => $host . ':' . $port,
        ];
    }

    /**
     * @return array<non-empty-uppercase-string, string>
     */
    private function getRequestHeaders(RequestInterface $request): array
    {
        $result = [];

        foreach ($request->headers as $name => $value) {
            $result['HTTP_' . \strtr($name, self::LOWER, self::UPPER)] = $value;
        }

        /** @var array<non-empty-uppercase-string, string> */
        return $result;
    }

    /**
     * @return array{
     *     REQUEST_TIME_FLOAT: float,
     *     REQUEST_TIME: int,
     *     ...
     * }
     */
    private function getRequestTime(): array
    {
        if ($this->clock !== null) {
            return $this->getRequestTimeFromPsr20($this->clock);
        }

        $microtime = \microtime(true);

        return [
            'REQUEST_TIME_FLOAT' => $microtime,
            'REQUEST_TIME' => (int) $microtime,
        ];
    }

    /**
     * @return array{
     *     REQUEST_TIME_FLOAT: float,
     *     REQUEST_TIME: int,
     *     ...
     * }
     */
    private function getRequestTimeFromPsr20(ClockInterface $clock): array
    {
        $now = $clock->now();
        $microtime = $now->getTimestamp();

        return [
            'REQUEST_TIME_FLOAT' => $microtime + .000_001 * $now->getMicrosecond(),
            'REQUEST_TIME' => $microtime,
        ];
    }
}
