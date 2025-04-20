<?php

declare(strict_types=1);

namespace Boson\WebView;

use JetBrains\PhpStorm\ExpectedValues;

final class Url implements \Stringable
{
    /**
     * Contains the user information component of the URI/URL.
     *
     * If no scheme is present, this method MUST return a {@see null} value.
     *
     * If a user is present in the URI, this will return that value;
     * additionally, if the password is also present, it will be appended to the
     * user value, with a colon (":") separating the values.
     *
     * The trailing "@" character is not part of the user information and MUST
     * NOT be added.
     */
    public ?string $userInfo {
        get => $this->userInfo ??= $this->buildUserInfo();
    }

    /**
     * Contains the authority component of the URI/URL.
     *
     * If no scheme is present, this method MUST return a {@see null} value.
     *
     * If the port component is not set or is the standard port for the current
     * scheme, it SHOULD NOT be included.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     */
    public ?string $authority {
        get => $this->authority ??= $this->buildAuthority();
    }

    public function __construct(
        /**
         * Contains the scheme component of the URI/URL.
         *
         * If no scheme is present, this method MUST return a {@see null} value.
         *
         * The value returned MUST be normalized to lowercase, per RFC 3986
         * Section 3.1.
         *
         * The trailing ":" character is not part of the scheme and MUST NOT be
         * added.
         *
         * @see https://tools.ietf.org/html/rfc3986#section-3.1
         */
        #[ExpectedValues(values: [
            'https', 'http', 'wss', 'ws', 'data',
            'file', 'ftp', 'mailto', 'tel', 'imap',
            'irc', 'nntp', 'acap', 'icap', 'mtqp',
            'urn', 'javascript', 'resource', 'view-source',
        ])]
        public readonly ?string $scheme,
        /**
         * Contains the user component of the URI/URL.
         */
        public readonly ?string $user,
        /**
         * Contains the password component of the URI/URL.
         */
        #[\SensitiveParameter]
        public readonly ?string $password,
        /**
         * Contains the host component of the URI/URL.
         *
         * If no host is present this method MUST return a {@see null} value.
         *
         * The value returned MUST be normalized to lowercase, per RFC 3986
         * Section 3.2.2.
         *
         * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
         */
        public readonly ?string $host,
        /**
         * Contains the port component of the URI/URL.
         *
         * If a port is present, and it is non-standard for the current scheme,
         * this method MUST return it as an integer. If the port is the standard port
         * used with the current scheme, this method SHOULD return {@see null}.
         *
         * If no port is present, and no scheme is present, this method MUST return
         * a {@see null} value.
         *
         * If no port is present, but a scheme is present, this method MAY return
         * the standard port for that scheme, but SHOULD return {@see null}.
         *
         * @var int<0, 65535>|null
         */
        public readonly ?int $port,
        /**
         * Contains the path component of the URI/URL.
         *
         * The path can either be empty or absolute (starting with a slash) or
         * rootless (not starting with a slash). Implementations MUST support all
         * three syntaxes.
         *
         * Normally, the empty path "" and absolute path "/" are considered equal as
         * defined in RFC 7230 Section 2.7.3. But this method MUST NOT automatically
         * do this normalization because in contexts with a trimmed base path, e.g.
         * the front controller, this difference becomes significant. It's the task
         * of the user to handle both "" and "/".
         *
         * The value returned MUST be percent-encoded, but MUST NOT double-encode
         * any characters. To determine what characters to encode, please refer to
         * RFC 3986, Sections 2 and 3.3.
         *
         * As an example, if the value should include a slash ("/") not intended as
         * delimiter between path segments, that value MUST be passed in encoded
         * form (e.g., "%2F") to the instance.
         *
         * @see https://tools.ietf.org/html/rfc3986#section-2
         * @see https://tools.ietf.org/html/rfc3986#section-3.3
         */
        public readonly string $path,
        /**
         * Contains the query string of the URI/URL.
         *
         * If no host is present this method MUST return a {@see null} value.
         *
         * The leading "?" character is not part of the query and MUST NOT be
         * added.
         *
         * The value returned MUST be percent-encoded, but MUST NOT double-encode
         * any characters. To determine what characters to encode, please refer to
         * RFC 3986, Sections 2 and 3.4.
         *
         * As an example, if a value in a key/value pair of the query string should
         * include an ampersand ("&") not intended as a delimiter between values,
         * that value MUST be passed in encoded form (e.g., "%26") to the instance.
         *
         * @see https://tools.ietf.org/html/rfc3986#section-2
         * @see https://tools.ietf.org/html/rfc3986#section-3.4
         */
        public readonly ?string $query,
        /**
         * Contains the fragment component of the URI/URL.
         *
         * If no host is present this method MUST return a {@see null} value.
         *
         * The leading "#" character is not part of the fragment and MUST be omitted.
         *
         * The value returned MUST be percent-encoded, but MUST NOT double-encode
         * any characters. To determine what characters to encode, please refer to
         * RFC 3986, Sections 2 and 3.5.
         *
         * @see https://tools.ietf.org/html/rfc3986#section-2
         * @see https://tools.ietf.org/html/rfc3986#section-3.5
         */
        public readonly ?string $fragment,
        /**
         * An optional full URI/URLURI/URL value. Required for string {@see __toString()}
         * conversion.
         *
         * If value is defined as {@see null}, it will be created based
         * on the current state of the URI/URL instance.
         */
        private ?string $value = null,
    ) {}

    private function buildAuthority(): ?string
    {
        if ($this->host === null) {
            return null;
        }

        $authority = $this->host;

        if ($this->port !== null) {
            $authority .= ':' . $this->port;
        }

        if ($this->user === null) {
            return $authority;
        }

        $authority = '@' . $authority;

        if ($this->password === null) {
            return $this->user . $authority;
        }

        return $this->user . ':' . $this->password . $authority;
    }

    /**
     * Builder method for
     */
    private function buildUserInfo(): ?string
    {
        return match (null) {
            $this->password => $this->user,
            default => $this->user . ':' . $this->password,
        };
    }

    private function buildUriString(): string
    {
        $uri = '';

        if ($this->scheme !== null) {
            $uri .= $this->scheme . ':';
        }

        if ($this->authority !== null) {
            $uri .= '//' . $this->authority;
        }

        $uri .= $this->path;

        if ($this->query !== null) {
            $uri .= '?' . $this->query;
        }

        if ($this->fragment !== null) {
            $uri .= '#' . $this->fragment;
        }

        return $uri;
    }

    public function __toString(): string
    {
        return $this->value ??= $this->buildUriString();
    }
}
