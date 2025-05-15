<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Schemes\Event;

use Boson\Http\RequestInterface;
use Boson\Http\ResponseInterface;
use Boson\Shared\Marker\AsWebViewIntention;
use Boson\WebView\WebView;

#[AsWebViewIntention]
class SchemeRequestReceived extends SchemesApiIntention
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
