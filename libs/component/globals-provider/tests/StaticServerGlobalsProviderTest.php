<?php

declare(strict_types=1);

namespace Boson\Component\GlobalsProvider\Tests;

use Boson\Component\GlobalsProvider\StaticServerGlobalsProvider;
use Boson\Contracts\Http\RequestInterface;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\MockObject\MockObject;

#[Group('boson-php/globals-provider')]
final class StaticServerGlobalsProviderTest extends TestCase
{
    private RequestInterface&MockObject $request;

    protected function setUp(): void
    {
        $this->request = $this->createMock(RequestInterface::class);

        parent::setUp();
    }

    public function testCreateWithDefaultServer(): void
    {
        $provider = new StaticServerGlobalsProvider();

        $globals = $provider->getServerGlobals($this->request);

        self::assertArrayHasKey('DOCUMENT_ROOT', $globals);
        self::assertArrayHasKey('SERVER_NAME', $globals);
        self::assertArrayHasKey('SERVER_PORT', $globals);
        self::assertArrayHasKey('SERVER_SOFTWARE', $globals);
        self::assertSame('Boson Runtime', $globals['SERVER_SOFTWARE']);
    }

    public function testCreateWithCustomServer(): void
    {
        $customServer = [
            'DOCUMENT_ROOT' => '/custom/root',
            'SERVER_NAME' => 'example.com',
            'SERVER_PORT' => '8080',
            'SERVER_SOFTWARE' => 'Custom Server',
            'CUSTOM_KEY' => 'custom_value',
        ];

        $provider = new StaticServerGlobalsProvider($customServer);

        $globals = $provider->getServerGlobals($this->request);

        self::assertSame('/custom/root', $globals['DOCUMENT_ROOT']);
        self::assertSame('example.com', $globals['SERVER_NAME']);
        self::assertSame('8080', $globals['SERVER_PORT']);
        self::assertSame('Custom Server', $globals['SERVER_SOFTWARE']);
        self::assertSame('custom_value', $globals['CUSTOM_KEY']);
    }

    public function testFilterNonScalarValues(): void
    {
        $server = [
            'ARRAY_VALUE' => ['not', 'scalar'],
            'OBJECT_VALUE' => new \stdClass(),
            'NULL_VALUE' => null,
            'STRING_VALUE' => 'valid',
            'INT_VALUE' => 42,
            'BOOL_VALUE' => true,
        ];

        $provider = new StaticServerGlobalsProvider($server);

        $globals = $provider->getServerGlobals($this->request);

        self::assertArrayNotHasKey('ARRAY_VALUE', $globals);
        self::assertArrayNotHasKey('OBJECT_VALUE', $globals);
        self::assertArrayNotHasKey('NULL_VALUE', $globals);
        self::assertSame('valid', $globals['STRING_VALUE']);
        self::assertSame(42, $globals['INT_VALUE']);
        self::assertSame(true, $globals['BOOL_VALUE']);
    }

    public function testFilterEmptyKeys(): void
    {
        $server = [
            '' => 'empty key',
            'VALID_KEY' => 'valid value',
        ];

        $provider = new StaticServerGlobalsProvider($server);

        $globals = $provider->getServerGlobals($this->request);

        self::assertArrayNotHasKey('', $globals);
        self::assertSame('valid value', $globals['VALID_KEY']);
    }

    public function testDocumentRootFromScriptFilename(): void
    {
        $server = [
            'SCRIPT_FILENAME' => '/var/www/script.php',
        ];

        $provider = new StaticServerGlobalsProvider($server);

        $globals = $provider->getServerGlobals($this->request);

        self::assertSame('/var/www', $globals['DOCUMENT_ROOT']);
    }

    public function testDocumentRootFromCurrentDirectory(): void
    {
        $server = [];

        $provider = new StaticServerGlobalsProvider($server);

        $globals = $provider->getServerGlobals($this->request);

        self::assertSame((string) \getcwd(), $globals['DOCUMENT_ROOT']);
    }

    public function testDefaultServerValues(): void
    {
        $server = [];

        $provider = new StaticServerGlobalsProvider($server);

        $globals = $provider->getServerGlobals($this->request);

        self::assertSame('0.0.0.0', $globals['SERVER_NAME']);
        self::assertSame('0', $globals['SERVER_PORT']);
        self::assertSame('Boson Runtime', $globals['SERVER_SOFTWARE']);
    }
}
