<?php

declare(strict_types=1);

namespace Boson\Tests\Unit\Http\Uri;

use Boson\Http\Uri\UserInfo;
use PHPUnit\Framework\Attributes\Group;

#[Group('serafim/boson')]
final class UserInfoTest extends UriTestCase
{
    public function testCreateUserInfoWithoutPassword(): void
    {
        $userInfo = new UserInfo(user: 'john');

        self::assertEquals('john', $userInfo->user);
        self::assertNull($userInfo->password);
        self::assertEquals('john', (string) $userInfo);
    }

    public function testCreateUserInfoWithPassword(): void
    {
        $userInfo = new UserInfo(user: 'john', password: 'secret');

        self::assertEquals('john', $userInfo->user);
        self::assertEquals('secret', $userInfo->password);
        self::assertEquals('john:secret', (string) $userInfo);
    }

    public function testUserInfoIsImmutable(): void
    {
        $userInfo = new UserInfo(user: 'john');

        $this->expectException(\Error::class);
        $userInfo->user = 'jane';
    }
}
