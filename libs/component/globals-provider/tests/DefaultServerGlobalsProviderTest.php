<?php

declare(strict_types=1);

namespace Boson\Component\GlobalsProvider\Tests;

use Boson\Component\GlobalsProvider\DefaultServerGlobalsProvider;
use Boson\Component\Http\Request;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Clock\ClockInterface;

#[Group('boson-php/globals-provider')]
final class DefaultServerGlobalsProviderTest extends TestCase
{
    private ClockInterface&MockObject $clock;

    protected function setUp(): void
    {
        $this->clock = $this->createMock(ClockInterface::class);

        parent::setUp();
    }

    public function testCreateWithDefaultDelegate(): void
    {
        $provider = new DefaultServerGlobalsProvider();

        $globals = $provider->getServerGlobals(new Request());

        self::assertArrayHasKey('REQUEST_TIME', $globals);
        self::assertArrayHasKey('REQUEST_TIME_FLOAT', $globals);
        self::assertArrayHasKey('REQUEST_METHOD', $globals);
        self::assertArrayHasKey('REQUEST_URI', $globals);
        self::assertArrayHasKey('HTTP_HOST', $globals);
    }

    public function testCreateWithCustomDelegate(): void
    {
        $provider = new DefaultServerGlobalsProvider();

        $globals = $provider->getServerGlobals(new Request());

        self::assertArrayHasKey('REQUEST_TIME', $globals);
        self::assertArrayHasKey('REQUEST_TIME_FLOAT', $globals);
        self::assertArrayHasKey('REQUEST_METHOD', $globals);
        self::assertArrayHasKey('REQUEST_URI', $globals);
        self::assertArrayHasKey('HTTP_HOST', $globals);
    }

    public function testCreateWithCustomClock(): void
    {
        $now = new \DateTimeImmutable('2024-01-01 12:00:00');
        $this->clock->method('now')->willReturn($now);

        $provider = new DefaultServerGlobalsProvider(clock: $this->clock);

        $globals = $provider->getServerGlobals(new Request());

        self::assertSame($now->getTimestamp(), $globals['REQUEST_TIME']);
        self::assertSame((float) $now->getTimestamp(), $globals['REQUEST_TIME_FLOAT']);
    }

    public function testRequestInfo(): void
    {
        $provider = new DefaultServerGlobalsProvider();

        $globals = $provider->getServerGlobals(new Request(
            method: 'POST',
            url: 'https://example.com/path?query=value',
            headers: ['Host' => 'example.com'],
        ));

        self::assertSame('POST', $globals['REQUEST_METHOD']);
        self::assertSame('/path?query=value', $globals['REQUEST_URI']);
        self::assertSame('example.com', $globals['HTTP_HOST']);
    }

    public function testRequestHeaders(): void
    {
        $provider = new DefaultServerGlobalsProvider();

        $globals = $provider->getServerGlobals(new Request(
            headers: [
                'Content-Type' => 'application/json',
                'User-Agent' => 'Test Browser',
                'X-Custom-Header' => 'custom value',
            ],
        ));

        self::assertSame('application/json', $globals['HTTP_CONTENT_TYPE']);
        self::assertSame('Test Browser', $globals['HTTP_USER_AGENT']);
        self::assertSame('custom value', $globals['HTTP_X_CUSTOM_HEADER']);
    }

    public function testHeaderNameNormalization(): void
    {
        $provider = new DefaultServerGlobalsProvider();

        $globals = $provider->getServerGlobals(new Request(
            headers: [
                'content-type' => 'application/json',
                'User-Agent' => 'Test Browser',
                'x-custom-header' => 'custom value',
            ]
        ));

        self::assertSame('application/json', $globals['HTTP_CONTENT_TYPE']);
        self::assertSame('Test Browser', $globals['HTTP_USER_AGENT']);
        self::assertSame('custom value', $globals['HTTP_X_CUSTOM_HEADER']);
    }
}
