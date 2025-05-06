<?php

declare(strict_types=1);

namespace Boson\WebView\Internal\Rpc;

/**
 * @internal This is an internal library interface, please do not use it in your code.
 * @psalm-internal Boson\WebView
 */
interface RpcResponderInterface
{
    /**
     * @param non-empty-string $id
     */
    public function resolve(string $id, mixed $result): void;

    /**
     * @param non-empty-string $id
     */
    public function reject(string $id, \Throwable $reason): void;
}
