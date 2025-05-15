<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

interface SecurityApiInterface
{
    /**
     * The `$isSecureContext` read-only property of the document
     * returns a {@see bool} indicating whether the current context
     * is secure ({@see true}) or not ({@see false}).
     *
     * A secure context is a Window or Worker for which certain minimum
     * standards of authentication and confidentiality are met. Many Web
     * APIs and features are accessible only in a secure context. The
     * primary goal of secure contexts is to prevent MITM attackers from
     * accessing powerful APIs that could further compromise the victim
     * of an attack.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/Security/Secure_Contexts
     * @link https://developer.mozilla.org/en-US/docs/Web/API/Window/isSecureContext
     */
    public bool $isSecureContext {
        get;
    }
}
