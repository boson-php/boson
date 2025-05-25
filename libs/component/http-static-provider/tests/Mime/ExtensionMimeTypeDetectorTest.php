<?php

declare(strict_types=1);

namespace Boson\Component\Http\Static\Tests\Mime;

use Boson\Component\Http\Static\Mime\ExtensionMimeTypeDetector;
use Boson\Component\Http\Static\Mime\MimeTypeDetectorInterface;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/http-static-provider')]
final class ExtensionMimeTypeDetectorTest extends TestCase
{
    public function testDetectCommonMimeTypes(): void
    {
        $detector = new ExtensionMimeTypeDetector();

        self::assertSame('text/html', $detector->findMimeTypeByFile('index.html'));
        self::assertSame('text/css', $detector->findMimeTypeByFile('styles.css'));
        self::assertSame('application/javascript', $detector->findMimeTypeByFile('script.js'));
        self::assertSame('image/jpeg', $detector->findMimeTypeByFile('photo.jpg'));
        self::assertSame('image/png', $detector->findMimeTypeByFile('image.png'));
        self::assertSame('application/pdf', $detector->findMimeTypeByFile('document.pdf'));
    }

    public function testDetectMimeTypeWithMultipleDots(): void
    {
        $detector = new ExtensionMimeTypeDetector();

        self::assertSame('text/html', $detector->findMimeTypeByFile('index.min.html'));
        self::assertSame('application/javascript', $detector->findMimeTypeByFile('script.min.js'));
    }

    public function testDetectMimeTypeWithUppercaseExtension(): void
    {
        $detector = new ExtensionMimeTypeDetector();

        self::assertSame('text/html', $detector->findMimeTypeByFile('index.HTML'));
        self::assertSame('text/css', $detector->findMimeTypeByFile('styles.CSS'));
        self::assertSame('application/javascript', $detector->findMimeTypeByFile('script.JS'));
    }

    public function testDetectMimeTypeWithNoExtension(): void
    {
        $detector = new ExtensionMimeTypeDetector();

        self::assertNull($detector->findMimeTypeByFile('file'));
        self::assertNull($detector->findMimeTypeByFile('file.'));
    }

    public function testDetectMimeTypeWithUnknownExtension(): void
    {
        $detector = new ExtensionMimeTypeDetector();

        self::assertNull($detector->findMimeTypeByFile('file.vasya'));
    }

    public function testDelegateIsCalledWhenNoMimeTypeFound(): void
    {
        $delegate = $this->createMock(MimeTypeDetectorInterface::class);
        $delegate->expects(self::once())
            ->method('findMimeTypeByFile')
            ->with('file.vasya')
            ->willReturn('application/vasya');

        $detector = new ExtensionMimeTypeDetector($delegate);

        self::assertSame('application/vasya', $detector->findMimeTypeByFile('file.vasya'));
    }

    public function testDelegateIsNotCalledWhenMimeTypeFound(): void
    {
        $delegate = $this->createMock(MimeTypeDetectorInterface::class);
        $delegate->expects(self::never())
            ->method('findMimeTypeByFile');

        $detector = new ExtensionMimeTypeDetector($delegate);

        self::assertSame('text/html', $detector->findMimeTypeByFile('index.html'));
    }

    public function testDetectMimeTypeWithPath(): void
    {
        $detector = new ExtensionMimeTypeDetector();

        self::assertSame('text/html', $detector->findMimeTypeByFile('/path/to/file.html'));
        self::assertSame('text/css', $detector->findMimeTypeByFile('\\path\\to\\file.css'));
        self::assertSame('application/javascript', $detector->findMimeTypeByFile('./path/to/file.js'));
    }

    public function testDetectMimeTypeWithSpecialExtensions(): void
    {
        $detector = new ExtensionMimeTypeDetector();

        self::assertSame('application/x-7z-compressed', $detector->findMimeTypeByFile('archive.7z'));
        self::assertSame('application/x-bzip2', $detector->findMimeTypeByFile('archive.bz2'));
        self::assertSame('application/gzip', $detector->findMimeTypeByFile('archive.gz'));
        self::assertSame('application/vnd.rar', $detector->findMimeTypeByFile('archive.rar'));
        self::assertSame('application/x-tar', $detector->findMimeTypeByFile('archive.tar'));
        self::assertSame('application/zip', $detector->findMimeTypeByFile('archive.zip'));
    }
}
