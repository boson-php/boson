<?php

declare(strict_types=1);

namespace Boson\Http\Uri;

use Boson\Http\Uri\Scheme\SchemeValueObjectImpl;
use Boson\Http\Uri\Scheme\StandardScheme;
use Boson\Shared\ValueObject\BackedEnumLikeImpl;

/**
 * @internal this constant cannot be autoloaded, please use {@see Scheme::Http} instead
 */
const SCHEME_HTTP = new StandardScheme('http', 80);

/**
 * @internal this constant cannot be autoloaded, please use {@see Scheme::Https} instead
 */
const SCHEME_HTTPS = new StandardScheme('https', 443);

/**
 * @internal this constant cannot be autoloaded, please use {@see Scheme::Data} instead
 */
const SCHEME_DATA = new StandardScheme('data');

/**
 * @internal this constant cannot be autoloaded, please use {@see Scheme::File} instead
 */
const SCHEME_FILE = new StandardScheme('file');

/**
 * @internal this constant cannot be autoloaded, please use {@see Scheme::Ftp} instead
 */
const SCHEME_FTP = new StandardScheme('ftp', 21);

/**
 * @internal this constant cannot be autoloaded, please use {@see Scheme::Gopher} instead
 */
const SCHEME_GOPHER = new StandardScheme('gopher', 70);

/**
 * @internal this constant cannot be autoloaded, please use {@see Scheme::Ws} instead
 */
const SCHEME_WS = new StandardScheme('ws', 80);

/**
 * @internal this constant cannot be autoloaded, please use {@see Scheme::Wss} instead
 */
const SCHEME_WSS = new StandardScheme('wss', 443);

final readonly class Scheme implements SchemeInterface
{
    /** @use BackedEnumLikeImpl<SchemeInterface, non-empty-lowercase-string> */
    use BackedEnumLikeImpl;
    use SchemeValueObjectImpl;

    /**
     * HTTP (Hypertext Transfer Protocol) is an application layer protocol
     * in the Internet protocol suite model for distributed, collaborative,
     * hypermedia information systems.
     *
     * HTTP is the foundation of data communication for the World Wide Web,
     * where hypertext documents include hyperlinks to other resources that
     * the user can easily access, for example by a mouse click or by tapping
     * the screen in a web browser.
     */
    public const StandardScheme Http = SCHEME_HTTP;

    /**
     * Hypertext Transfer Protocol Secure (HTTPS) is an extension of the
     * Hypertext Transfer Protocol (HTTP). It uses encryption for secure
     * communication over a computer network, and is widely used on the Internet.
     *
     * In HTTPS, the communication protocol is encrypted using Transport
     * Layer Security (TLS) or, formerly, Secure Sockets Layer (SSL).
     * The protocol is therefore also referred to as HTTP over TLS,
     * or HTTP over SSL.
     */
    public const StandardScheme Https = SCHEME_HTTPS;

    /**
     * Data URLs, URLs prefixed with the `data:` scheme, allow content creators
     * to embed small files inline in documents. They were formerly known
     * as 'data URIs' until that name was retired by the WHATWG.
     *
     * Data URLs are composed of four parts:
     * - A prefix (`data:`);
     * - A MIME type indicating the type of data
     * - An optional base64 token if non-textual
     * - And the data itself.
     *
     * ```
     * data:[<media-type>][;base64],<data>
     * ```
     *
     * For example, the `text/plain` data `Hello, World!`. Note how the  comma
     * is {@link https://developer.mozilla.org/en-US/docs/Glossary/Percent-encoding percent-encoded}
     * as `%2C`, and the space character as `%20`.
     *
     * ```
     * data:,Hello%2C%20World%21
     * ```
     *
     * The base64-encoded version of the above.
     *
     * ```
     * data:text/plain;base64,SGVsbG8sIFdvcmxkIQ==
     * ```
     *
     * An HTML document with `<h1>Hello, World!</h1>`.
     *
     * ```
     * data:text/html,%3Ch1%3EHello%2C%20World%21%3C%2Fh1%3E
     * ```
     *
     * An HTML document with `<script>alert('hi');</script>` that executes a
     * JavaScript alert. Note that the closing script tag is required.
     *
     * ```
     * data:text/html,%3Cscript%3Ealert%28%27hi%27%29%3B%3C%2Fscript%3E
     * ```
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/URI/Reference/Schemes/data
     */
    public const StandardScheme Data = SCHEME_DATA;

    /**
     * The host-specific file names.
     */
    public const StandardScheme File = SCHEME_FILE;

    /**
     * FTP (File Transfer Protocol) is an insecure protocol for transferring
     * files from one host to another over the Internet.
     *
     * For many years it was the defacto standard way of transferring files,
     * but as it is inherently insecure, it is no longer supported by many
     * hosting accounts. Instead you should use SFTP (a secure, encrypted
     * version of FTP) or another secure method for transferring files like
     * Rsync over SSH.
     *
     * @link https://developer.mozilla.org/en-US/docs/Glossary/FTP
     */
    public const StandardScheme Ftp = SCHEME_FTP;

    /**
     * @link https://datatracker.ietf.org/doc/html/rfc4266
     */
    public const StandardScheme Gopher = SCHEME_GOPHER;

    /**
     * @link https://developer.mozilla.org/en-US/docs/Web/API/WebSockets_API
     */
    public const StandardScheme Ws = SCHEME_WS;

    /**
     * @link https://developer.mozilla.org/en-US/docs/Web/API/WebSockets_API
     */
    public const StandardScheme Wss = SCHEME_WSS;

    /**
     * @var non-empty-lowercase-string
     */
    public string $name;

    /**
     * @param non-empty-string $name
     */
    public function __construct(string $name)
    {
        $this->name = \strtolower($name);
    }

    protected static function caseKeyFor(object $case): string
    {
        assert($case instanceof SchemeInterface);

        return $case->name;
    }
}
