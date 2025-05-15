<?php

declare(strict_types=1);

namespace Boson\WebView\Api\SecurityApi;

use Boson\WebView\Api\SecurityApiInterface;
use Boson\WebView\Api\WebViewApi;
use Boson\WebView\WebViewState;

/**
 * Provides information about the security context of the WebView.
 */
final class WebViewSecurity extends WebViewApi implements SecurityApiInterface
{
    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array DEFAULT_SOFTWARE_INSECURE_SCHEMES = [
        'data',
        'about',
    ];

    /**
     * Indicates whether the current context is considered secure.
     */
    public bool $isSecureContext {
        get => $this->getSecurityContext();
    }

    /**
     * Cached value of the real security context status from JavaScript
     * mapped to URL schemes.
     *
     * @var array<non-empty-lowercase-string, bool>
     */
    private array $realSecurityValuesForSchemes = [];

    /**
     * Parses the current WebView URL and returns its scheme in lowercase.
     *
     * Returns {@see null} if the scheme cannot be parsed or is empty.
     *
     * @return non-empty-lowercase-string|null
     */
    private function findCurrentScheme(): ?string
    {
        $scheme = \parse_url($this->webview->url, \PHP_URL_SCHEME);

        if ($scheme === '' || !\is_string($scheme)) {
            return null;
        }

        /** @var non-empty-lowercase-string */
        return \strtolower($scheme);
    }

    /**
     * Determines the security context of the WebView.
     *
     * If the WebView state is ready, it attempts to get the real security
     * status from the JavaScript context. Otherwise, it falls back to a
     * software-based security check based on the URL scheme.
     */
    private function getSecurityContext(): bool
    {
        if ($this->webview->state === WebViewState::Ready) {
            $scheme = $this->findCurrentScheme();

            if ($scheme === null) {
                return false;
            }

            return $this->realSecurityValuesForSchemes[$scheme] ??= $this->getRealSecurity();
        }

        return $this->getSoftwareSecurity();
    }

    /**
     * Retrieves the security status from the JavaScript
     * `window.isSecureContext` property.
     *
     * This method should only be called when the WebView state is ready.
     */
    private function getRealSecurity(): bool
    {
        return (bool) $this->webview->data->get('window.isSecureContext');
    }

    /**
     * Performs a software-based security check based on the WebView's
     * current URL scheme.
     *
     * A context is considered insecure if its scheme is empty, {@see null},
     * or present in the {@see DEFAULT_SOFTWARE_INSECURE_SCHEMES} list.
     */
    private function getSoftwareSecurity(): bool
    {
        $scheme = $this->findCurrentScheme();

        if ($scheme === null) {
            return false;
        }

        return !\in_array($scheme, self::DEFAULT_SOFTWARE_INSECURE_SCHEMES, true);
    }
}
