<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Schemes\Event {

    use Boson\Contracts\Http\RequestInterface;
    use Boson\Contracts\Http\ResponseInterface;
    use Boson\WebView\WebView;

    /**
     * @deprecated Please use {@see SchemeRequestReceive} intention instead
     */
    final class SchemeRequestReceived extends SchemesApiIntention
    {
        public function __construct(
            WebView $subject,
            public readonly RequestInterface $request,
            public ?ResponseInterface $response = null,
            ?int $time = null,
        ) {
            parent::__construct($subject, $time);
        }
    }

}
