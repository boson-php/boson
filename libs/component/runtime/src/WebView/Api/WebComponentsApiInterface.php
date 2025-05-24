<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

use Boson\WebView\Api\WebComponents\MutableWebComponentsMapInterface;

/**
 * Allows to register custom web components, check their existence,
 * and get their count.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/API/Web_components/Using_custom_elements
 */
interface WebComponentsApiInterface extends
    MutableWebComponentsMapInterface {}
