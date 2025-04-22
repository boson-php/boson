<?php

declare(strict_types=1);

namespace Boson\Tests\Unit\Http\Uri;

use Boson\Http\Uri\Authority;
use Boson\Http\Uri\UserInfo;
use PHPUnit\Framework\Attributes\Group;

#[Group('serafim/boson')]
final class AuthorityTest extends UriTestCase
{
    public function testCreateAuthorityWithHostOnly(): void
    {
        $authority = new Authority(host: 'example.com');

        self::assertSame('example.com', $authority->host);
        self::assertNull($authority->port);
        self::assertNull($authority->user);
        self::assertNull($authority->password);
        self::assertSame('example.com', (string)$authority);
    }

    public function testCreateAuthorityWithPort(): void
    {
        $authority = new Authority(host: 'example.com', port: 8080);

        self::assertSame('example.com', $authority->host);
        self::assertSame(8080, $authority->port);
        self::assertNull($authority->user);
        self::assertNull($authority->password);
        self::assertSame('example.com:8080', (string)$authority);
    }

    public function testCreateAuthorityWithUserInfo(): void
    {
        $userInfo = new UserInfo(user: 'john', password: 'secret');
        $authority = new Authority(host: 'example.com', userInfo: $userInfo);

        self::assertSame('example.com', $authority->host);
        self::assertNull($authority->port);
        self::assertSame('john', $authority->user);
        self::assertSame('secret', $authority->password);
        self::assertSame('john:secret@example.com', (string)$authority);
    }

    public function testCreateAuthorityWithUserInfoAndPort(): void
    {
        $userInfo = new UserInfo(user: 'john', password: 'secret');
        $authority = new Authority(host: 'example.com', port: 8443, userInfo: $userInfo);

        self::assertSame('example.com', $authority->host);
        self::assertSame(8443, $authority->port);
        self::assertSame('john', $authority->user);
        self::assertSame('secret', $authority->password);
        self::assertSame('john:secret@example.com:8443', (string)$authority);
    }
}
