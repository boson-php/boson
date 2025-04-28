<?php

declare(strict_types=1);

namespace Boson\Bridge\Http\Server;

use Boson\Http\RequestInterface;

/**
 * @template-covariant T of mixed = mixed
 *
 * @template-extends GlobalsDecoderInterface<T>
 */
interface RequestTypeAwareDecoderInterface extends GlobalsDecoderInterface
{
    /**
     * Returns {@see true} in case of implementation supports
     * request conversion or {@see false} instead.
     */
    public function isSupports(RequestInterface $request): bool;
}
