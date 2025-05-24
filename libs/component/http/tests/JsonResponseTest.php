<?php

declare(strict_types=1);

namespace Boson\Component\Http\Tests;

use Boson\Component\Http\JsonResponse;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/http')]
final class JsonResponseTest extends TestCase
{
    public function testCreateEmpty(): void
    {
        $response = new JsonResponse();

        self::assertSame('null', $response->body);
        self::assertCount(1, $response->headers);
        self::assertTrue($response->headers->has('content-type'));
        self::assertSame('application/json', $response->headers->first('content-type'));
        self::assertSame(200, $response->status);
    }

    public function testCreateWithSimpleData(): void
    {
        $response = new JsonResponse(['key' => 'value']);

        self::assertSame('{"key":"value"}', $response->body);
        self::assertCount(1, $response->headers);
        self::assertTrue($response->headers->has('content-type'));
        self::assertSame('application/json', $response->headers->first('content-type'));
        self::assertSame(200, $response->status);
    }

    public function testCreateWithComplexData(): void
    {
        $data = [
            'string' => 'value',
            'number' => 42,
            'boolean' => true,
            'null' => null,
            'array' => [1, 2, 3],
            'object' => ['nested' => 'value'],
        ];

        $response = new JsonResponse($data);

        self::assertSame(
            '{"string":"value","number":42,"boolean":true,"null":null,"array":[1,2,3],"object":{"nested":"value"}}',
            $response->body
        );
        self::assertCount(1, $response->headers);
        self::assertTrue($response->headers->has('content-type'));
        self::assertSame('application/json', $response->headers->first('content-type'));
        self::assertSame(200, $response->status);
    }

    public function testCreateWithCustomHeaders(): void
    {
        $response = new JsonResponse(
            data: ['key' => 'value'],
            headers: [
                'X-Custom' => 'value',
            ],
        );

        self::assertSame('{"key":"value"}', $response->body);
        self::assertCount(2, $response->headers);
        self::assertTrue($response->headers->has('content-type'));
        self::assertSame('application/json', $response->headers->first('content-type'));
        self::assertTrue($response->headers->has('x-custom'));
        self::assertSame('value', $response->headers->first('x-custom'));
        self::assertSame(200, $response->status);
    }

    public function testCreateWithCustomStatus(): void
    {
        $response = new JsonResponse(
            data: ['error' => 'Not Found'],
            status: 404,
        );

        self::assertSame('{"error":"Not Found"}', $response->body);
        self::assertCount(1, $response->headers);
        self::assertTrue($response->headers->has('content-type'));
        self::assertSame('application/json', $response->headers->first('content-type'));
        self::assertSame(404, $response->status);
    }

    public function testCreateWithCustomJsonEncodingFlags(): void
    {
        $response = new JsonResponse(
            data: ['key' => 'value'],
            jsonEncodingFlags: \JSON_PRETTY_PRINT,
        );

        self::assertSame("{\n    \"key\": \"value\"\n}", $response->body);
        self::assertCount(1, $response->headers);
        self::assertTrue($response->headers->has('content-type'));
        self::assertSame('application/json', $response->headers->first('content-type'));
        self::assertSame(200, $response->status);
    }

    public function testCreateWithHtmlSafeEncoding(): void
    {
        $response = new JsonResponse(
            data: ['html' => '<script>alert("xss")</script>'],
        );

        self::assertSame(
            '{"html":"\u003Cscript\u003Ealert(\u0022xss\u0022)\u003C\/script\u003E"}',
            $response->body
        );
        self::assertCount(1, $response->headers);
        self::assertTrue($response->headers->has('content-type'));
        self::assertSame('application/json', $response->headers->first('content-type'));
        self::assertSame(200, $response->status);
    }

    public function testCreateWithExistingContentType(): void
    {
        $response = new JsonResponse(
            data: ['key' => 'value'],
            headers: [
                'Content-Type' => 'application/json; charset=utf-8',
            ],
        );

        self::assertSame('{"key":"value"}', $response->body);
        self::assertCount(1, $response->headers);
        self::assertTrue($response->headers->has('content-type'));
        self::assertSame('application/json; charset=utf-8', $response->headers->first('content-type'));
        self::assertSame(200, $response->status);
    }

    public function testCreateWithInvalidJsonData(): void
    {
        $this->expectException(\JsonException::class);

        new JsonResponse(\fopen('php://memory', 'r'));
    }
}
