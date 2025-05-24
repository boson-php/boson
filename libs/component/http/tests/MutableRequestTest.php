<?php

declare(strict_types=1);

namespace Boson\Component\Http\Tests;

use Boson\Component\Http\MutableRequest;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/http')]
final class MutableRequestTest extends TestCase
{
    public function testCreateEmpty(): void
    {
        $request = new MutableRequest();

        self::assertSame('GET', $request->method);
        self::assertSame('about:blank', $request->url);
        self::assertCount(0, $request->headers);
        self::assertSame('', $request->body);
    }

    public function testCreateWithCustomMethod(): void
    {
        $request = new MutableRequest('POST');

        self::assertSame('POST', $request->method);
        self::assertSame('about:blank', $request->url);
        self::assertCount(0, $request->headers);
        self::assertSame('', $request->body);
    }

    public function testCreateWithCustomUrl(): void
    {
        $request = new MutableRequest(
            method: 'GET',
            url: 'https://example.com/api',
        );

        self::assertSame('GET', $request->method);
        self::assertSame('https://example.com/api', $request->url);
        self::assertCount(0, $request->headers);
        self::assertSame('', $request->body);
    }

    public function testCreateWithCustomHeaders(): void
    {
        $request = new MutableRequest(
            method: 'GET',
            url: '/',
            headers: [
                'Content-Type' => 'application/json',
                'X-Custom' => 'value',
            ],
        );

        self::assertSame('GET', $request->method);
        self::assertSame('/', $request->url);
        self::assertCount(2, $request->headers);
        self::assertTrue($request->headers->has('content-type'));
        self::assertSame('application/json', $request->headers->first('content-type'));
        self::assertTrue($request->headers->has('x-custom'));
        self::assertSame('value', $request->headers->first('x-custom'));
        self::assertSame('', $request->body);
    }

    public function testCreateWithCustomBody(): void
    {
        $request = new MutableRequest(
            method: 'POST',
            url: '/',
            headers: [],
            body: '{"key": "value"}',
        );

        self::assertSame('POST', $request->method);
        self::assertSame('/', $request->url);
        self::assertCount(0, $request->headers);
        self::assertSame('{"key": "value"}', $request->body);
    }

    public function testModifyMethod(): void
    {
        $request = new MutableRequest();
        $request->method = 'PUT';

        self::assertSame('PUT', $request->method);
    }

    public function testModifyUrl(): void
    {
        $request = new MutableRequest();
        $request->url = 'https://example.com/api/v2';

        self::assertSame('https://example.com/api/v2', $request->url);
    }

    public function testModifyHeaders(): void
    {
        $request = new MutableRequest();
        $request->headers->set('Content-Type', 'application/json');
        $request->headers->add('X-Custom', 'value1');
        $request->headers->add('X-Custom', 'value2');

        self::assertTrue($request->headers->has('content-type'));
        self::assertSame('application/json', $request->headers->first('content-type'));
        self::assertTrue($request->headers->has('x-custom'));
        self::assertSame(['value1', 'value2'], $request->headers->all('x-custom'));
    }

    public function testModifyBody(): void
    {
        $request = new MutableRequest();
        $request->body = '{"key": "new-value"}';

        self::assertSame('{"key": "new-value"}', $request->body);
    }

    public function testCreateFromRequest(): void
    {
        $original = new MutableRequest(
            method: 'POST',
            url: 'https://example.com/api',
            headers: [
                'Content-Type' => 'application/json',
            ],
            body: '{"key": "value"}',
        );

        $request = MutableRequest::createFromRequest($original);

        self::assertSame('POST', $request->method);
        self::assertSame('https://example.com/api', $request->url);
        self::assertCount(1, $request->headers);
        self::assertTrue($request->headers->has('content-type'));
        self::assertSame('application/json', $request->headers->first('content-type'));
        self::assertSame('{"key": "value"}', $request->body);
    }

    public function testCreateFromRequestWithCloning(): void
    {
        $original = new MutableRequest(
            method: 'POST',
            url: 'https://example.com/api',
            headers: [
                'Content-Type' => 'application/json',
            ],
            body: '{"key": "value"}',
        );

        $request = MutableRequest::createFromRequest($original);

        self::assertNotSame($original->headers, $request->headers);
    }

    public function testMethodCaseInsensitivity(): void
    {
        $request = new MutableRequest();
        $request->method = 'put';

        self::assertSame('PUT', $request->method);
    }

    public function testFullModification(): void
    {
        $request = new MutableRequest();

        $request->method = 'PUT';
        $request->url = 'https://example.com/api/v2';
        $request->headers->set('Content-Type', 'application/json');
        $request->body = '{"key": "value"}';

        self::assertSame('PUT', $request->method);
        self::assertSame('https://example.com/api/v2', $request->url);
        self::assertTrue($request->headers->has('content-type'));
        self::assertSame('application/json', $request->headers->first('content-type'));
        self::assertSame('{"key": "value"}', $request->body);
    }
}
