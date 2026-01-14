<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Schemes\Event;

use Boson\Contracts\Http\Component\StatusCodeInterface;
use Boson\Contracts\Http\RequestInterface;
use Boson\Shared\Marker\AsWebViewIntention;
use Boson\WebView\WebView;

#[AsWebViewIntention]
final class SchemeRequestReject extends SchemesApiIntention
{
    public function __construct(
        WebView $subject,
        public readonly RequestInterface $request,
        public StatusCodeInterface $status,
        ?int $time = null,
    ) {
        parent::__construct($subject, $time);
    }
}
