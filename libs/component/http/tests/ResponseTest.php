<?php

declare(strict_types=1);

namespace Boson\Component\Http\Tests;

use Boson\Component\Http\Response;
use Boson\Tests\Unit\TestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/http')]
final class ResponseTest extends TestCase
{
    public function testCreateEmpty(): void
    {
        $response = new Response();

        self::assertSame('', $response->body);
        self::assertCount(1, $response->headers);
        self::assertTrue($response->headers->has('content-type'));
        self::assertSame('text/html; charset=utf-8', $response->headers->first('content-type'));
        self::assertSame(200, $response->status);
    }

    public function testCreateWithCustomBody(): void
    {
        $response = new Response('Hello World');

        self::assertSame('Hello World', $response->body);
        self::assertCount(1, $response->headers);
        self::assertTrue($response->headers->has('content-type'));
        self::assertSame('text/html; charset=utf-8', $response->headers->first('content-type'));
        self::assertSame(200, $response->status);
    }

    public function testCreateWithCustomHeaders(): void
    {
        $response = new Response(
            body: 'Hello World',
            headers: [
                'Content-Type' => 'application/json',
                'X-Custom' => 'value',
            ],
        );

        self::assertSame('Hello World', $response->body);
        self::assertCount(2, $response->headers);
        self::assertTrue($response->headers->has('content-type'));
        self::assertSame('application/json', $response->headers->first('content-type'));
        self::assertTrue($response->headers->has('x-custom'));
        self::assertSame('value', $response->headers->first('x-custom'));
        self::assertSame(200, $response->status);
    }

    public function testCreateWithCustomStatus(): void
    {
        $response = new Response(
            body: 'Not Found',
            status: 404,
        );

        self::assertSame('Not Found', $response->body);
        self::assertCount(1, $response->headers);
        self::assertTrue($response->headers->has('content-type'));
        self::assertSame('text/html; charset=utf-8', $response->headers->first('content-type'));
        self::assertSame(404, $response->status);
    }

    public function testModifyBody(): void
    {
        $response = new Response('Hello');
        $response->body = 'World';

        self::assertSame('World', $response->body);
    }

    public function testModifyHeaders(): void
    {
        $response = new Response();
        $response->headers->set('X-Custom', 'value');

        self::assertTrue($response->headers->has('x-custom'));
        self::assertSame('value', $response->headers->first('x-custom'));
    }

    public function testModifyStatus(): void
    {
        $response = new Response();
        $response->status = 500;

        self::assertSame(500, $response->status);
    }

    public function testRemoveContentLengthWhenTransferEncodingPresent(): void
    {
        $response = new Response(
            headers: [
                'Content-Length' => '123',
                'Transfer-Encoding' => 'chunked',
            ],
        );

        self::assertFalse($response->headers->has('content-length'));
        self::assertTrue($response->headers->has('transfer-encoding'));
        self::assertSame('chunked', $response->headers->first('transfer-encoding'));
    }

    public function testKeepContentLengthWhenNoTransferEncoding(): void
    {
        $response = new Response(
            headers: [
                'Content-Length' => '123',
            ],
        );

        self::assertTrue($response->headers->has('content-length'));
        self::assertSame('123', $response->headers->first('content-length'));
    }
} 