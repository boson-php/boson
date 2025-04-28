<?php

declare(strict_types=1);

namespace Boson\Bridge\Static\Mime;

final readonly class ExtensionFileDetector implements FileDetectorInterface
{
    /**
     * List of supported by the Chromium project mime types.
     *
     * @var non-empty-array<non-empty-string, non-empty-string>
     *
     * @link https://github.com/chromium/chromium/blob/137.0.7122.1/net/base/mime_util.cc#L95-L255
     */
    public const array MIME_TYPES_FOR_EXTENSIONS = [
        'webm' => 'video/webm',
        // has been redefined by the "audio/mp3"
        // 'mp3' => 'audio/mpeg',
        'wasm' => 'application/wasm',
        'crx' => 'application/x-chrome-extension',
        'xhtml' => 'application/xhtml+xml',
        'xht' => 'application/xhtml+xml',
        'xhtm' => 'application/xhtml+xml',
        'flac' => 'audio/flac',
        'mp3' => 'audio/mp3',
        'ogg' => 'audio/ogg',
        'oga' => 'audio/ogg',
        'opus' => 'audio/ogg',
        'wav' => 'audio/wav',
        // has been defined as "video/webm"
        // 'webm' => 'audio/webm',
        'm4a' => 'audio/x-m4a',
        'avif' => 'image/avif',
        'gif' => 'image/gif',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'apng' => 'image/apng',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        'webp' => 'image/webp',
        'mht' => 'multipart/related',
        'mhtml' => 'multipart/related',
        'css' => 'text/css',
        'html' => 'text/html',
        'htm' => 'text/html',
        'shtml' => 'text/html',
        'shtm' => 'text/html',
        // has been redefined by the "application/javascript"
        // 'js' => 'text/javascript',
        'mjs' => 'text/javascript',
        'xml' => 'text/xml',
        'mp4' => 'video/mp4',
        'm4v' => 'video/mp4',
        'ogv' => 'video/ogg',
        'ogm' => 'video/ogg',
        'csv' => 'text/csv',
        'ico' => 'image/x-icon',
        'epub' => 'application/epub+zip',
        'woff' => 'application/font-woff',
        'gz' => 'application/gzip',
        'tgz' => 'application/gzip',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'doc' => 'application/msword',
        'dot' => 'application/msword',
        'bin' => 'application/octet-stream',
        'exe' => 'application/octet-stream',
        'com' => 'application/octet-stream',
        'pdf' => 'application/pdf',
        'p7m' => 'application/pkcs7-mime',
        'p7c' => 'application/pkcs7-mime',
        'p7z' => 'application/pkcs7-mime',
        'p7s' => 'application/pkcs7-signature',
        'ps' => 'application/postscript',
        'eps' => 'application/postscript',
        'ai' => 'application/postscript',
        'rdf' => 'application/rdf+xml',
        'rss' => 'application/rss+xml',
        'rtf' => 'application/rtf',
        'apk' => 'application/vnd.android.package-archive',
        'xul' => 'application/vnd.mozilla.xul+xml',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        // has been defined as "application/gzip"
        // 'gz' => 'application/x-gzip',
        // has been defined as "application/gzip"
        // 'tgz' => 'application/x-gzip',
        'm3u8' => 'application/x-mpegurl',
        'swf' => 'application/x-shockwave-flash',
        'swl' => 'application/x-shockwave-flash',
        'tar' => 'application/x-tar',
        'cer' => 'application/x-x509-ca-cert',
        'crt' => 'application/x-x509-ca-cert',
        'zip' => 'application/zip',
        'weba' => 'audio/webm',
        'bmp' => 'image/bmp',
        'jfif' => 'image/jpeg',
        'pjpeg' => 'image/jpeg',
        'pjp' => 'image/jpeg',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        // has been defined as "image/x-icon"
        // 'ico' => 'image/vnd.microsoft.icon',
        // has been defined as "image/png"
        // 'png' => 'image/x-png',
        'xbm' => 'image/x-xbitmap',
        'eml' => 'message/rfc822',
        'ics' => 'text/calendar',
        'ehtml' => 'text/html',
        'txt' => 'text/plain',
        'text' => 'text/plain',
        'vtt' => 'text/vtt',
        'sh' => 'text/x-sh',
        'xsl' => 'text/xml',
        'xbl' => 'text/xml',
        'xslt' => 'text/xml',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'woff2' => 'font/woff2',
    ];

    public function __construct(
        private ?FileDetectorInterface $delegate = null,
    ) {}

    public function detectByFile(string $pathname): ?string
    {
        $ext = \pathinfo($pathname, \PATHINFO_EXTENSION);

        return self::MIME_TYPES_FOR_EXTENSIONS[\strtolower($ext)]
            ?? $this->delegate?->detectByFile($pathname);
    }
}
