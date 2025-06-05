<?php

declare(strict_types=1);

namespace Boson\Component\Http\Body\Tests;

use Boson\Component\Http\Body\FormUrlEncodedDecoder;
use Boson\Component\Http\Request;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/http-body-decoder')]
final class FormUrlEncodedDecoderTest extends TestCase
{
    private FormUrlEncodedDecoder $decoder;

    protected function setUp(): void
    {
        $this->decoder = new FormUrlEncodedDecoder();

        parent::setUp();
    }

    public function testIsDecodableWithCorrectContentType(): void
    {
        $request = new Request(
            headers: ['content-type' => ['application/x-www-form-urlencoded']]
        );

        self::assertTrue($this->decoder->isDecodable($request));
    }

    public function testIsDecodableWithIncorrectContentType(): void
    {
        $request = new Request(
            headers: ['content-type' => ['application/json']]
        );

        self::assertFalse($this->decoder->isDecodable($request));
    }

    public function testIsDecodableWithMissingContentType(): void
    {
        $request = new Request();

        self::assertFalse($this->decoder->isDecodable($request));
    }

    public function testDecodeSimpleKeyValue(): void
    {
        $request = new Request(
            headers: ['content-type' => ['application/x-www-form-urlencoded']],
            body: 'key=value'
        );

        $result = $this->decoder->decode($request);

        self::assertSame(['key' => 'value'], $result);
    }

    public function testDecodeMultipleKeyValues(): void
    {
        $request = new Request(
            headers: ['content-type' => ['application/x-www-form-urlencoded']],
            body: 'key1=value1&key2=value2'
        );

        $result = $this->decoder->decode($request);

        self::assertSame(
            [
                'key1' => 'value1',
                'key2' => 'value2'
            ],
            $result
        );
    }

    public function testDecodeWithArrayNotation(): void
    {
        $request = new Request(
            headers: ['content-type' => ['application/x-www-form-urlencoded']],
            body: 'items[]=1&items[]=2&items[]=3'
        );

        $result = $this->decoder->decode($request);

        self::assertSame(
            [
                'items' => ['1', '2', '3']
            ],
            $result
        );
    }

    public function testDecodeWithNestedArrays(): void
    {
        $request = new Request(
            headers: ['content-type' => ['application/x-www-form-urlencoded']],
            body: 'user[name]=John&user[age]=30'
        );

        $result = $this->decoder->decode($request);

        self::assertSame(
            [
                'user' => [
                    'name' => 'John',
                    'age' => '30'
                ]
            ],
            $result
        );
    }

    public function testDecodeWithUrlEncodedValues(): void
    {
        $request = new Request(
            headers: ['content-type' => ['application/x-www-form-urlencoded']],
            body: 'message=Hello%20World%21'
        );

        $result = $this->decoder->decode($request);

        self::assertSame(['message' => 'Hello World!'], $result);
    }

    public function testDecodeWithEmptyValue(): void
    {
        $request = new Request(
            headers: ['content-type' => ['application/x-www-form-urlencoded']],
            body: 'key='
        );

        $result = $this->decoder->decode($request);

        self::assertSame(['key' => ''], $result);
    }

    public function testDecodeWithEmptyBody(): void
    {
        $request = new Request(
            headers: ['content-type' => ['application/x-www-form-urlencoded']],
            body: ''
        );

        $result = $this->decoder->decode($request);

        self::assertSame([], $result);
    }
}
