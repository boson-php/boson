<?php

declare(strict_types=1);

namespace Boson\Component\Http\Body\Tests\MultipartFormData;

use Boson\Component\Http\Body\MultipartFormData\FormDataBoundary;
use Boson\Component\Http\Body\MultipartFormData\FormDataBoundaryFactory;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('boson-php/http-body-decoder')]
final class FormDataBoundaryFactoryTest extends TestCase
{
    public function testCreateFromSimpleBoundary(): void
    {
        $boundary = new FormDataBoundaryFactory()
            ->tryCreateFromContentType(
                'multipart/form-data; boundary=ExampleBosonBoundary',
            );

        self::assertNotNull($boundary);
        self::assertInstanceOf(FormDataBoundary::class, $boundary);
        self::assertSame('ExampleBosonBoundary', $boundary->value);
        self::assertSame('--ExampleBosonBoundary', $boundary->segment);
        self::assertSame('--ExampleBosonBoundary--', $boundary->end);
    }

    public function testCreateFromQuotedBoundary(): void
    {
        $boundary = new FormDataBoundaryFactory()
            ->tryCreateFromContentType(
                'multipart/form-data; boundary="ExampleBosonBoundary"',
            );

        self::assertNotNull($boundary);
        self::assertInstanceOf(FormDataBoundary::class, $boundary);
        self::assertSame('ExampleBosonBoundary', $boundary->value);
    }

    public function testCreateFromBoundaryWithAdditionalParams(): void
    {
        $boundary = new FormDataBoundaryFactory()
            ->tryCreateFromContentType(
                'multipart/form-data; boundary=ExampleBosonBoundary; charset=UTF-8',
            );

        self::assertNotNull($boundary);
        self::assertInstanceOf(FormDataBoundary::class, $boundary);
        self::assertSame('ExampleBosonBoundary', $boundary->value);
    }

    public function testCreateFromQuotedBoundaryWithAdditionalParams(): void
    {
        $boundary = new FormDataBoundaryFactory()
            ->tryCreateFromContentType(
                'multipart/form-data; boundary="ExampleBosonBoundary"; charset=UTF-8',
            );

        self::assertNotNull($boundary);
        self::assertInstanceOf(FormDataBoundary::class, $boundary);
        self::assertSame('ExampleBosonBoundary', $boundary->value);
    }

    public function testCreateFromMissingBoundary(): void
    {
        $boundary = new FormDataBoundaryFactory()
            ->tryCreateFromContentType(
                'multipart/form-data; charset=UTF-8',
            );

        self::assertNull($boundary);
    }

    public function testCreateFromEmptyBoundary(): void
    {
        $boundary = new FormDataBoundaryFactory()
            ->tryCreateFromContentType(
                'multipart/form-data; boundary=',
            );

        self::assertNull($boundary);
    }

    public function testCreateFromEmptyQuotedBoundary(): void
    {
        $boundary = new FormDataBoundaryFactory()
            ->tryCreateFromContentType(
                'multipart/form-data; boundary=""',
            );

        self::assertNull($boundary);
    }

    public function testCreateFromUnclosedQuotedBoundary(): void
    {
        $boundary = new FormDataBoundaryFactory()
            ->tryCreateFromContentType(
                'multipart/form-data; boundary="ExampleBosonBoundary',
            );

        self::assertNull($boundary);
    }

    public function testCreateFromInvalidContentType(): void
    {
        $boundary = new FormDataBoundaryFactory()
            ->tryCreateFromContentType(
                'application/json',
            );

        self::assertNull($boundary);
    }

    public function testCreateFromBoundaryWithSpaces(): void
    {
        $boundary = new FormDataBoundaryFactory()
            ->tryCreateFromContentType(
                'multipart/form-data; boundary= ExampleBosonBoundary ',
            );

        self::assertNotNull($boundary);
        self::assertInstanceOf(FormDataBoundary::class, $boundary);
        self::assertSame('ExampleBosonBoundary', $boundary->value);
    }

    public function testCreateFromQuotedBoundaryWithSpaces(): void
    {
        $boundary = new FormDataBoundaryFactory()
            ->tryCreateFromContentType(
                'multipart/form-data; boundary=" ExampleBosonBoundary "',
            );

        self::assertNotNull($boundary);
        self::assertInstanceOf(FormDataBoundary::class, $boundary);
        self::assertSame('ExampleBosonBoundary', $boundary->value);
    }
}
