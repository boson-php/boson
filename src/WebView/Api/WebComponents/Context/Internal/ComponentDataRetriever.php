<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Context\Internal;

use Boson\Shared\Marker\BlockingOperation;
use Boson\WebView\Api\Data\DataRetrieverInterface;
use Boson\WebView\Api\DataApiInterface;
use JetBrains\PhpStorm\Language;
use React\Promise\PromiseInterface;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView\Api\WebComponents
 */
final readonly class ComponentDataRetriever extends ComponentAwareBuilder implements
    DataRetrieverInterface
{
    /**
     * @param non-empty-string $id
     */
    public function __construct(
        string $id,
        private DataApiInterface $data,
    ) {
        parent::__construct($id);
    }

    #[\Override]
    protected function build(#[Language('JavaScript')] string $code): string
    {
        return parent::build('return ' . $code);
    }

    public function defer(#[Language('JavaScript')] string $code): PromiseInterface
    {
        return $this->data->defer($this->build($code));
    }

    #[BlockingOperation]
    public function get(#[Language('JavaScript')] string $code, ?float $timeout = null): mixed
    {
        return $this->data->get($this->build($code), $timeout);
    }
}
