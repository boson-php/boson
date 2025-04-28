<?php

declare(strict_types=1);

namespace Boson\Bridge\Http\Server;

use Boson\Bridge\Server\non;
use Boson\Http\HeadersInterface;
use Boson\Http\RequestInterface;

/**
 * Decode and extract PHP `$_SERVER` globals.
 *
 * @template-implements GlobalsDecoderInterface<scalar>
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Bridge
 */
final readonly class ServerGlobalsDecoder implements GlobalsDecoderInterface
{
    /**
     * @var array<non-empty-string, scalar>
     */
    private array $server;

    /**
     * @param array<array-key, mixed>|null $defaultServerGlobalParams
     */
    public function __construct(
        ?array $defaultServerGlobalParams = null,
    ) {
        $this->server = $this->filterServerGlobalParams(
            server: $defaultServerGlobalParams ?? $this->getNormalizedServerGlobals(),
        );
    }

    /**
     * @return array<array-key, mixed>
     */
    private function getNormalizedServerGlobals(): array
    {
        $server = $_SERVER;

        // Normalize document root in case of document root is empty or undefined
        if (($server['DOCUMENT_ROOT'] ?? '') === '') {
            $server['DOCUMENT_ROOT'] = match (true) {
                isset($server['SCRIPT_FILENAME']) && \is_string($server['SCRIPT_FILENAME'])
                => \dirname($server['SCRIPT_FILENAME']),
                default => (string) \getcwd(),
            };
        }

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

    /**
     * @return array<non-empty-string, scalar>
     */
    public function decode(RequestInterface $request): array
    {
        return [
            ...$this->createServerFromGlobals(),
            ...$this->createServerFromRequest($request),
            ...$this->createServerFromHeaders($request->headers),
        ];
    }

    /**
     * @return array<non-empty-string, scalar>
     */
    private function createServerFromGlobals(): array
    {
        return [
            'SERVER_NAME' => '0.0.0.0',
            'SERVER_PORT' => '0',
            ...$this->server,
            'SERVER_SOFTWARE' => 'Boson Runtime',
            'REQUEST_TIME_FLOAT' => $time = \microtime(true),
            'REQUEST_TIME' => (int) $time,
        ];
    }

    /**
     * @return array<non-empty-string, scalar>
     */
    private function createServerFromRequest(RequestInterface $request): array
    {
        $result = [
            'REQUEST_METHOD' => $request->method->name,
            'QUERY_STRING' => $request->url->query->toString(),
            'PATH_INFO' => $request->url->path->toString(),
        ];

        // Build request uri
        $result['REQUEST_URI'] = $request->url->query->count() > 0
            ? \sprintf('%s?%s', $result['PATH_INFO'], $result['QUERY_STRING'])
            : $result['PATH_INFO'];

        // Apply addr or set 127.0.0.1 by default
        $result['REMOTE_ADDR'] = $request->url->authority->host ?? '127.0.0.1';
        // Apply port or sett 80 by default
        $result['REMOTE_PORT'] = (string) ($request->url->authority->port ?? 80);
        // Build http host
        $result['HTTP_HOST'] = $result['REMOTE_ADDR'] . ':' . $result['REMOTE_PORT'];

        return $result;
    }

    /**
     * @return array<non-empty-uppercase-string, string>
     */
    private function createServerFromHeaders(HeadersInterface $headers): array
    {
        $result = [];

        foreach ($headers as $header => $value) {
            $converted = $this->convertHeaderNameToServerFormat($header);

            $result[$converted] = (string) $value;
        }

        return $result;
    }

    /**
     * @param non-empty-string $name
     *
     * @return non-empty-uppercase-string
     */
    private function convertHeaderNameToServerFormat(string $name): string
    {
        $name = \str_replace('-', '_', $name);

        return 'HTTP_' . \strtoupper($name);
    }
}
