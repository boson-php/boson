<?php

namespace Boson\WebView\Event {

    use Boson\Shared\Marker\AsWebViewIntention;
    use Boson\WebView\Api\Schemes\Event\SchemeRequestReceived;

    /**
     * @deprecated Please use {@see SchemeRequestReceived} instead.
     */
    #[AsWebViewIntention]
    class WebViewRequest extends SchemeRequestReceived {}
}

