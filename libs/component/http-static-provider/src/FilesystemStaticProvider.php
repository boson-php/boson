<?php

declare(strict_types=1);

namespace Boson\Component\Http\Static;

use Boson\Component\Http\Response;
use Boson\Component\Http\Static\Common\MimeTypeContentTypeDetector;
use Boson\Component\Http\Static\Mime\ExtensionMimeTypeDetector;
use Boson\Component\Http\Static\Mime\MimeTypeDetectorInterface;
use Boson\Contracts\Http\RequestInterface;
use Boson\Contracts\Http\ResponseInterface;

final readonly class FilesystemStaticProvider implements StaticProviderInterface
{
    /**
     * @var list<non-empty-string>
     */
    private array $directories;

    private MimeTypeContentTypeDetector $contentTypeDetector;

    /**
     * @param iterable<mixed, non-empty-string>|non-empty-string $root
     *        List of root (public directories) for files lookup
     */
    public function __construct(
        iterable|string $root = [],
        MimeTypeDetectorInterface $mimeTypeDetector = new ExtensionMimeTypeDetector()
    ) {
        $this->contentTypeDetector = new MimeTypeContentTypeDetector(
            mimeDetector: $mimeTypeDetector,
        );

        if (\is_string($root)) {
            $root = [$root];
        }

        $this->directories = \iterator_to_array($root, false);
    }

    /**
     * @return non-empty-string|null
     */
    private function findPathnameForExistingFile(RequestInterface $request): ?string
    {
        $path = \parse_url($request->url, \PHP_URL_PATH);

        if (!\is_string($path) || $path === '') {
            return null;
        }

        foreach ($this->directories as $root) {
            $pathname = $root . '/' . $path;

            if (!\is_file($pathname) || !\is_readable($pathname)) {
                continue;
            }

            return $pathname;
        }

        return null;
    }

    public function findFileByRequest(RequestInterface $request): ?ResponseInterface
    {
        $pathname = $this->findPathnameForExistingFile($request);

        if ($pathname === null) {
            return null;
        }

        $contentType = $this->contentTypeDetector->findContentTypeByFile($pathname);

        return new Response(
            body: (string) \file_get_contents($pathname),
            headers: ['content-type' => $contentType],
        );
    }
}
