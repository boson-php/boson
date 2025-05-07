<?php

declare(strict_types=1);

namespace Boson\WebView\Internal\Rpc;

use Boson\WebView\Api\ScriptsApiInterface;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView
 */
final readonly class DefaultRpcResponder implements RpcResponderInterface
{
    /**
     * @var non-empty-string
     */
    public const string DEFAULT_CONTEXT = 'window.boson.rpc';

    public function __construct(
        private ScriptsApiInterface $scriptsApi,
        /**
         * @var non-empty-string
         */
        private string $context = self::DEFAULT_CONTEXT,
    ) {}

    /**
     * @throws \Throwable in case of pack error occurs
     */
    private function resultToString(mixed $result): string
    {
        return \json_encode($result, \JSON_THROW_ON_ERROR);
    }

    /**
     * Returns JavaScript code for resolving.
     *
     * @param non-empty-string $id
     *
     * @return non-empty-string
     * @throws \Throwable
     */
    private function packResolveAction(string $id, mixed $result): string
    {
        return \vsprintf('%s.resolve("%s", %s);', [
            $this->context,
            \addcslashes($id, '"'),
            $this->resultToString($result),
        ]);
    }

    /**
     * @return non-empty-string
     */
    private function failureToString(\Throwable $reason): string
    {
        return \vsprintf('%s: %s in %s on line %d', [
            $reason::class,
            $reason->getMessage(),
            $reason->getFile(),
            $reason->getLine(),
        ]);
    }

    /**
     * Returns JavaScript code for rejection.
     *
     * @param non-empty-string $id
     *
     * @return non-empty-string
     */
    private function packRejectAction(string $id, \Throwable $reason): string
    {
        return \vsprintf('%s.reject("%s", Error(`%s`));', [
            $this->context,
            \addcslashes($id, '"'),
            \addcslashes($this->failureToString($reason), '`\\'),
        ]);
    }

    public function resolve(string $id, mixed $result): void
    {
        try {
            $action = $this->packResolveAction($id, $result);

            $this->scriptsApi->eval($action);
        } catch (\Throwable $e) {
            $this->reject($id, $e);
        }
    }

    public function reject(string $id, \Throwable $reason): void
    {
        $action = $this->packRejectAction($id, $reason);

        $this->scriptsApi->eval($action);
    }
}
