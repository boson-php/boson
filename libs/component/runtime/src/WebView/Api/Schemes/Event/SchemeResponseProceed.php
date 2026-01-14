<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Schemes\Event;

use Boson\Contracts\Http\RequestInterface;
use Boson\Contracts\Http\ResponseInterface;
use Boson\Shared\Marker\AsWebViewEvent;
use Boson\WebView\WebView;

#[AsWebViewEvent]
final class SchemeResponseProceed extends SchemesApiEvent
{
    public function __construct(
        WebView $subject,
        public readonly RequestInterface $request,
        public readonly ResponseInterface $response,
        ?int $time = null,
    ) {
        parent::__construct($subject, $time);
    }
}
