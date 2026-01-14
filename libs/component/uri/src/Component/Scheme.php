<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Component\Uri\Component\Scheme\SchemeImpl;
use Boson\Contracts\Uri\Component\SchemeInterface;

require_once __DIR__ . '/Scheme/constants.php';

/**
 * Implements enum-like structure representing most popular known URI schemes.
 *
 * Note: Impossible to implement via native PHP enum due to lack of support
 *       for properties: https://externals.io/message/126332
 */
final readonly class Scheme implements SchemeInterface
{
    use SchemeImpl;

    /**
     * HTTP (Hypertext Transfer Protocol) is an application layer protocol
     * in the Internet protocol suite model for distributed, collaborative,
     * hypermedia information systems.
     *
     * HTTP is the foundation of data communication for the World Wide Web,
     * where hypertext documents include hyperlinks to other resources that
     * the user can easily access, for example by a mouse click or by tapping
     * the screen in a web browser.
     *
     * @noinspection PhpUndefinedConstantInspection
     */
    public const SchemeInterface Http = Scheme\HTTP;

    /**
     * Hypertext Transfer Protocol Secure (HTTPS) is an extension of the
     * Hypertext Transfer Protocol (HTTP). It uses encryption for secure
     * communication over a computer network and is widely used on the Internet.
     *
     * In HTTPS, the communication protocol is encrypted using Transport
     * Layer Security (TLS) or, formerly, Secure Sockets Layer (SSL).
     * The protocol is therefore also referred to as HTTP over TLS,
     * or HTTP over SSL.
     *
     * @noinspection PhpUndefinedConstantInspection
     */
    public const SchemeInterface Https = Scheme\HTTPS;

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
     *
     * @noinspection PhpUndefinedConstantInspection
     */
    public const SchemeInterface Data = Scheme\DATA;

    /**
     * The host-specific file names.
     *
     * @noinspection PhpUndefinedConstantInspection
     */
    public const SchemeInterface File = Scheme\FILE;

    /**
     * FTP (File Transfer Protocol) is an insecure protocol for transferring
     * files from one host to another over the Internet.
     *
     * For many years it was the defacto standard way of transferring files,
     * but as it is inherently insecure, it is no longer supported by many
     * hosting accounts. Instead, you should use SFTP (a secure, encrypted
     * version of FTP) or another secure method for transferring files like
     * Rsync over SSH.
     *
     * @link https://developer.mozilla.org/en-US/docs/Glossary/FTP
     *
     * @noinspection PhpUndefinedConstantInspection
     */
    public const SchemeInterface Ftp = Scheme\FTP;

    /**
     * @link https://datatracker.ietf.org/doc/html/rfc4266
     *
     * @noinspection PhpUndefinedConstantInspection
     */
    public const SchemeInterface Gopher = Scheme\GOPHER;

    /**
     * @link https://developer.mozilla.org/en-US/docs/Web/API/WebSockets_API
     *
     * @noinspection PhpUndefinedConstantInspection
     */
    public const SchemeInterface Ws = Scheme\WS;

    /**
     * @link https://developer.mozilla.org/en-US/docs/Web/API/WebSockets_API
     *
     * @noinspection PhpUndefinedConstantInspection
     */
    public const SchemeInterface Wss = Scheme\WSS;

    /**
     * @var non-empty-array<non-empty-lowercase-string, SchemeInterface>
     */
    private const array CASES = [
        'http' => self::Http,
        'https' => self::Https,
        'data' => self::Data,
        'file' => self::File,
        'ftp' => self::Ftp,
        'gopher' => self::Gopher,
        'ws' => self::Ws,
        'wss' => self::Wss,
    ];

    /**
     * Translates a string value into the corresponding {@see Scheme} case,
     * if any. If there is no matching case defined, it will return {@see null}.
     *
     * @api
     *
     * @param non-empty-string $value
     */
    public static function tryFrom(string $value): ?SchemeInterface
    {
        return self::CASES[\strtolower($value)] ?? null;
    }

    /**
     * Translates a string value into the corresponding {@see Scheme}
     * case, if any. If there is no matching case defined,
     * it will throw {@see \ValueError}.
     *
     * @api
     *
     * @param non-empty-string $value
     *
     * @throws \ValueError if there is no matching case defined
     */
    public static function from(string $value): SchemeInterface
    {
        return self::tryFrom($value)
            ?? throw new \ValueError(\sprintf(
                '"%s" is not a valid backing value for enum-like %s',
                $value,
                self::class,
            ));
    }

    /**
     * Return a packed {@see array} of all cases in an enumeration,
     * in order of declaration.
     *
     * @api
     *
     * @return non-empty-list<SchemeInterface>
     */
    public static function cases(): array
    {
        return \array_values(self::CASES);
    }
}
