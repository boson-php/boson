<?php

declare(strict_types=1);

namespace Boson\Bridge\Http\Server;

use Boson\Http\RequestInterface;

/**
 * @template-covariant T of mixed = mixed
 */
interface GlobalsDecoderInterface
{
    /**
     * @return array<non-empty-string, T>
     */
    public function decode(RequestInterface $request): array;
}
