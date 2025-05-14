<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi;

use JetBrains\PhpStorm\Language;

interface TemplateChangerInterface
{
    /**
     * Update client-side template
     */
    public function changeTemplate(#[Language('HTML')] string $html): void;
}
