<?php

declare(strict_types=1);

namespace Boson\Component\GlobalsProvider\Tests;

use Boson\Component\GlobalsProvider\CompoundServerGlobalsProvider;
use Boson\Component\GlobalsProvider\ServerGlobalsProviderInterface;
use Boson\Component\Http\Request;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/globals-provider')]
final class CompoundServerGlobalsProviderTest extends TestCase
{
    public function testCreateWithEmptyProviders(): void
    {
        $provider = new CompoundServerGlobalsProvider([]);

        $globals = $provider->getServerGlobals(new Request());

        self::assertEmpty($globals);
    }

    public function testCreateWithSingleProvider(): void
    {
        $mockProvider = $this->createMock(ServerGlobalsProviderInterface::class);
        $mockProvider->method('getServerGlobals')
            ->willReturn([
                'SERVER_NAME' => 'example.com',
                'SERVER_PORT' => '8080',
            ]);

        $provider = new CompoundServerGlobalsProvider([$mockProvider]);

        $globals = $provider->getServerGlobals(new Request());

        self::assertSame('example.com', $globals['SERVER_NAME']);
        self::assertSame('8080', $globals['SERVER_PORT']);
    }

    public function testCreateWithMultipleProviders(): void
    {
        $mockProvider1 = $this->createMock(ServerGlobalsProviderInterface::class);
        $mockProvider1->method('getServerGlobals')
            ->willReturn([
                'SERVER_NAME' => 'example.com',
                'SERVER_PORT' => '8080',
            ]);

        $mockProvider2 = $this->createMock(ServerGlobalsProviderInterface::class);
        $mockProvider2->method('getServerGlobals')
            ->willReturn([
                'DOCUMENT_ROOT' => '/var/www',
                'SERVER_SOFTWARE' => 'Test Server',
            ]);

        $provider = new CompoundServerGlobalsProvider([$mockProvider1, $mockProvider2]);

        $globals = $provider->getServerGlobals(new Request());

        self::assertSame('example.com', $globals['SERVER_NAME']);
        self::assertSame('8080', $globals['SERVER_PORT']);
        self::assertSame('/var/www', $globals['DOCUMENT_ROOT']);
        self::assertSame('Test Server', $globals['SERVER_SOFTWARE']);
    }

    public function testProviderOrderMatters(): void
    {
        $mockProvider1 = $this->createMock(ServerGlobalsProviderInterface::class);
        $mockProvider1->method('getServerGlobals')
            ->willReturn([
                'SERVER_NAME' => 'first.com',
            ]);

        $mockProvider2 = $this->createMock(ServerGlobalsProviderInterface::class);
        $mockProvider2->method('getServerGlobals')
            ->willReturn([
                'SERVER_NAME' => 'second.com',
            ]);

        $provider = new CompoundServerGlobalsProvider([$mockProvider1, $mockProvider2]);

        $globals = $provider->getServerGlobals(new Request());

        self::assertSame('second.com', $globals['SERVER_NAME']);
    }

    public function testCreateWithIterableProviders(): void
    {
        $mockProvider1 = $this->createMock(ServerGlobalsProviderInterface::class);
        $mockProvider1->method('getServerGlobals')
            ->willReturn(['KEY1' => 'value1']);

        $mockProvider2 = $this->createMock(ServerGlobalsProviderInterface::class);
        $mockProvider2->method('getServerGlobals')
            ->willReturn(['KEY2' => 'value2']);

        $providers = new \ArrayIterator([$mockProvider1, $mockProvider2]);
        $provider = new CompoundServerGlobalsProvider($providers);

        $globals = $provider->getServerGlobals(new Request());

        self::assertSame('value1', $globals['KEY1']);
        self::assertSame('value2', $globals['KEY2']);
    }

    public function testRequestIsPassedToAllProviders(): void
    {
        $request = new Request(
            method: 'POST',
            url: 'https://example.com/path',
            headers: ['Host' => 'example.com'],
        );

        $mockProvider1 = $this->createMock(ServerGlobalsProviderInterface::class);
        $mockProvider1->expects(self::once())
            ->method('getServerGlobals')
            ->with($request)
            ->willReturn(['KEY1' => 'value1']);

        $mockProvider2 = $this->createMock(ServerGlobalsProviderInterface::class);
        $mockProvider2->expects(self::once())
            ->method('getServerGlobals')
            ->with($request)
            ->willReturn(['KEY2' => 'value2']);

        $provider = new CompoundServerGlobalsProvider([$mockProvider1, $mockProvider2]);

        $provider->getServerGlobals($request);
    }
}
