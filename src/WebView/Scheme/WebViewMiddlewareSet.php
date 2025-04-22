<?php

declare(strict_types=1);

namespace Boson\WebView\Scheme;

use Boson\Http\Middleware\ComposeHandler;
use Boson\Http\Middleware\EmptyHandler;
use Boson\Http\Middleware\HandlerInterface;
use Boson\Http\Middleware\MiddlewareInterface;
use Boson\Http\Middleware\PipelineInterface;
use Boson\Http\RequestInterface;
use Boson\Http\ResponseInterface;

/**
 * @template-implements \IteratorAggregate<array-key, MiddlewareInterface>
 */
final class WebViewMiddlewareSet implements PipelineInterface, \IteratorAggregate, \Countable
{
    /**
     * List of registered HTTP middleware
     *
     * @var list<MiddlewareInterface>
     */
    private array $middleware = [];

    /**
     * Contains terminal handler for all middleware
     */
    private readonly HandlerInterface $terminal;

    /**
     * Contains composed middleware list
     */
    private ?HandlerInterface $composed {
        /**
         * @return HandlerInterface
         */
        get => $this->composed ??= $this->compose();
        set => $value;
    }

    /**
     * @param iterable<mixed, MiddlewareInterface> $middleware
     */
    public function __construct(
        /**
         * Scheme name of the middleware pipeline.
         *
         * @var non-empty-lowercase-string
         */
        public readonly string $scheme,
        iterable $middleware = [],
    ) {
        $this->terminal = new EmptyHandler();
        $this->middleware = \iterator_to_array($middleware, false);
    }

    /**
     * Adds new middleware to the end
     */
    public function append(MiddlewareInterface $middleware): void
    {
        $this->middleware[] = $middleware;
        $this->composed = null;
    }

    /**
     * Adds new middleware to the start
     */
    public function prepend(MiddlewareInterface $middleware): void
    {
        $this->middleware = [$middleware, ...$this->middleware];
        $this->composed = null;
    }

    /**
     * Reduce middleware list to single handler
     */
    private function compose(): HandlerInterface
    {
        $handler = $this->terminal;

        foreach (\array_reverse($this->middleware) as $middleware) {
            $handler = new ComposeHandler($middleware, $handler);
        }

        return $handler;
    }

    public function handle(RequestInterface $request): ?ResponseInterface
    {
        /** @phpstan-ignore-next-line : Composed property cannot return null */
        return $this->composed->handle($request);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->middleware);
    }

    public function count(): int
    {
        return \count($this->middleware);
    }
}
