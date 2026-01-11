<?php

declare(strict_types=1);

namespace Boson\WebView\Manager;

use Boson\WebView\WebView;

/**
 * @template-extends \Traversable<array-key, WebView>
 */
interface WebViewCollectionInterface extends \Traversable, \Countable
{
    /**
     * Gets count of available webviews.
     *
     * @return int<0, max>
     */
    public function count(): int;
}
