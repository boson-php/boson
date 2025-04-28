<?php

declare(strict_types=1);

namespace Boson\Bridge\Static;

use Boson\Bridge\Static\Mime\ExtensionFileDetector;
use Boson\Bridge\Static\Mime\FileDetectorInterface;
use Boson\Http\RequestInterface;
use Boson\Http\Response;
use Boson\Http\ResponseInterface;

final readonly class FilesystemStaticAdapter implements StaticAdapterInterface
{
    /**
     * @var non-empty-lowercase-string
     */
    private const string DEFAULT_CONTENT_TYPE = 'text/html';

    /**
     * @var list<non-empty-string>
     */
    private array $root;

    /**
     * @param iterable<mixed, non-empty-string>|non-empty-string $root
     *        List of root (public directories) for files lookup
     */
    public function __construct(
        iterable|string $root = [],
        private FileDetectorInterface $mimeDetector = new ExtensionFileDetector(),
    ) {
        if (\is_string($root)) {
            $root = [$root];
        }

        $this->root = \iterator_to_array(
            iterator: \is_iterable($root) ? $root : [$root],
            preserve_keys: false,
        );
    }

    public function lookup(RequestInterface $request): ?ResponseInterface
    {
        $path = $request->url->path;

        foreach ($this->root as $root) {
            $pathname = $root . '/' . $path;

            if (!\is_file($pathname) || !\is_readable($pathname)) {
                continue;
            }

            $mimeType = $this->mimeDetector->detectByFile($pathname)
                ?? self::DEFAULT_CONTENT_TYPE;

            if (\str_starts_with($mimeType, 'text/') && !\str_contains($mimeType, 'charset=')) {
                $mimeType .= '; charset=utf-8';
            }

            return new Response(
                body: (string) \file_get_contents($pathname),
                headers: ['content-type' => $mimeType]
            );
        }

        return null;
    }
}
