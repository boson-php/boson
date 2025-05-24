<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Scripts;

/**
 * @template-extends \Traversable<mixed, LoadedScript>
 */
interface ScriptsSetInterface extends \Traversable, \Countable
{
    /**
     * The number of registered scripts
     *
     * @return int<0, max>
     */
    public function count(): int;
}
