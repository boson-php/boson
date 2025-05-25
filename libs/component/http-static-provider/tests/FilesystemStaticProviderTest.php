<?php

declare(strict_types=1);

namespace Boson\Component\Http\Static\Tests;

use Boson\Component\Http\Request;
use Boson\Component\Http\Static\FilesystemStaticProvider;
use Boson\Component\Http\Static\Mime\MimeTypeDetectorInterface;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/http-static-provider')]
final class FilesystemStaticProviderTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = __DIR__ . '/temp/test-' . \uniqid();
        \mkdir($this->tempDir);

        parent::setUp();
    }

    public function testCreateWithSingleRoot(): void
    {
        $provider = new FilesystemStaticProvider($this->tempDir);

        self::assertInstanceOf(FilesystemStaticProvider::class, $provider);
    }

    public function testCreateWithMultipleRoots(): void
    {
        $root1 = $this->tempDir . '/root1';
        $root2 = $this->tempDir . '/root2';
        \mkdir($root1);
        \mkdir($root2);

        $provider = new FilesystemStaticProvider([$root1, $root2]);

        self::assertInstanceOf(FilesystemStaticProvider::class, $provider);
    }

    public function testFindFileInSingleRoot(): void
    {
        $file = $this->tempDir . '/test.html';
        \file_put_contents($file, '<html>Test</html>');

        $provider = new FilesystemStaticProvider($this->tempDir);
        $request = new Request(url: 'http://example.com/test.html');

        $response = $provider->findFileByRequest($request);

        self::assertNotNull($response);
        self::assertSame('<html>Test</html>', $response->body);
        self::assertSame('text/html; charset=utf-8', $response->headers->first('content-type'));
    }

    public function testFindFileInMultipleRoots(): void
    {
        $root1 = $this->tempDir . '/root1';
        $root2 = $this->tempDir . '/root2';
        \mkdir($root1);
        \mkdir($root2);

        \file_put_contents($root1 . '/test.html', '<html>Root1</html>');
        \file_put_contents($root2 . '/test.html', '<html>Root2</html>');

        $provider = new FilesystemStaticProvider([$root1, $root2]);
        $request = new Request(url: 'http://example.com/test.html');

        $response = $provider->findFileByRequest($request);

        self::assertNotNull($response);
        self::assertSame('<html>Root1</html>', $response->body);
    }

    public function testFindFileWithCustomMimeTypeDetector(): void
    {
        $file = $this->tempDir . '/test.xyz';
        \file_put_contents($file, 'Test content');

        $mimeDetector = $this->createMock(MimeTypeDetectorInterface::class);
        $mimeDetector->method('findMimeTypeByFile')
            ->willReturn('application/xyz');

        $provider = new FilesystemStaticProvider($this->tempDir, $mimeDetector);
        $request = new Request(url: 'http://example.com/test.xyz');

        $response = $provider->findFileByRequest($request);

        self::assertNotNull($response);
        self::assertSame('Test content', $response->body);
        self::assertSame('application/xyz', $response->headers->first('content-type'));
    }

    public function testFindNonExistentFile(): void
    {
        $provider = new FilesystemStaticProvider($this->tempDir);
        $request = new Request(url: 'http://example.com/nonexistent.html');

        $response = $provider->findFileByRequest($request);

        self::assertNull($response);
    }

    public function testFindFileWithInvalidUrl(): void
    {
        $provider = new FilesystemStaticProvider($this->tempDir);
        $request = new Request(url: 'http://example.com');

        $response = $provider->findFileByRequest($request);

        self::assertNull($response);
    }
}
