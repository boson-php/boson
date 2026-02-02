<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Tests\Component;

use Boson\Component\Uri\Component\UserInfo;
use Boson\Component\Uri\Tests\TestCase;
use Boson\Contracts\Uri\Component\UserInfoInterface;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/uri')]
final class UserInfoTest extends TestCase
{
    public function testConstructorWithUserOnly(): void
    {
        $userInfo = new UserInfo('username');

        self::assertSame('username', $userInfo->username);
        self::assertNull($userInfo->password);
    }

    public function testConstructorWithUserAndPassword(): void
    {
        $userInfo = new UserInfo('username', 'password');

        self::assertSame('username', $userInfo->username);
        self::assertSame('password', $userInfo->password);
    }

    public function testConstructorWithEmptyPassword(): void
    {
        $userInfo = new UserInfo('username', '');

        self::assertSame('username', $userInfo->username);
        self::assertSame('', $userInfo->password);
    }

    public function testToStringWithUserOnly(): void
    {
        $userInfo = new UserInfo('username');

        self::assertSame('username', (string) $userInfo);
        self::assertSame('username', $userInfo->toString());
    }

    public function testToStringWithUserAndPassword(): void
    {
        $userInfo = new UserInfo('username', 'password');

        self::assertSame('username:password', (string) $userInfo);
        self::assertSame('username:password', $userInfo->toString());
    }

    public function testToStringWithEmptyPassword(): void
    {
        $userInfo = new UserInfo('username', '');

        self::assertSame('username:', (string) $userInfo);
        self::assertSame('username:', $userInfo->toString());
    }

    public function testToStringWithSpecialCharactersInUser(): void
    {
        $userInfo = new UserInfo('user-name', 'password');

        self::assertSame('user-name:password', (string) $userInfo);
        self::assertSame('user-name:password', $userInfo->toString());
    }

    public function testToStringWithSpecialCharactersInPassword(): void
    {
        $userInfo = new UserInfo('username', 'pass-word');

        self::assertSame('username:pass-word', (string) $userInfo);
        self::assertSame('username:pass-word', $userInfo->toString());
    }

    public function testToStringWithUnderscoresInUser(): void
    {
        $userInfo = new UserInfo('user_name', 'password');

        self::assertSame('user_name:password', (string) $userInfo);
        self::assertSame('user_name:password', $userInfo->toString());
    }

    public function testToStringWithUnderscoresInPassword(): void
    {
        $userInfo = new UserInfo('username', 'pass_word');

        self::assertSame('username:pass_word', (string) $userInfo);
        self::assertSame('username:pass_word', $userInfo->toString());
    }

    public function testToStringWithNumbersInUser(): void
    {
        $userInfo = new UserInfo('user123', 'password');

        self::assertSame('user123:password', (string) $userInfo);
        self::assertSame('user123:password', $userInfo->toString());
    }

    public function testToStringWithNumbersInPassword(): void
    {
        $userInfo = new UserInfo('username', 'pass123');

        self::assertSame('username:pass123', (string) $userInfo);
        self::assertSame('username:pass123', $userInfo->toString());
    }

    public function testEqualsWithSameInstance(): void
    {
        $userInfo = new UserInfo('username', 'password');

        self::assertTrue($userInfo->equals($userInfo));
    }

    public function testEqualsWithIdenticalUserInfo(): void
    {
        $userInfo1 = new UserInfo('username', 'password');
        $userInfo2 = new UserInfo('username', 'password');

        self::assertTrue($userInfo1->equals($userInfo2));
        self::assertTrue($userInfo2->equals($userInfo1));
    }

    public function testEqualsWithDifferentUser(): void
    {
        $userInfo1 = new UserInfo('user1', 'password');
        $userInfo2 = new UserInfo('user2', 'password');

        self::assertFalse($userInfo1->equals($userInfo2));
        self::assertFalse($userInfo2->equals($userInfo1));
    }

    public function testEqualsWithDifferentPassword(): void
    {
        $userInfo1 = new UserInfo('username', 'pass1');
        $userInfo2 = new UserInfo('username', 'pass2');

        self::assertFalse($userInfo1->equals($userInfo2));
        self::assertFalse($userInfo2->equals($userInfo1));
    }

    public function testEqualsWithPasswordVsNoPassword(): void
    {
        $userInfo1 = new UserInfo('username', 'password');
        $userInfo2 = new UserInfo('username');

        self::assertFalse($userInfo1->equals($userInfo2));
        self::assertFalse($userInfo2->equals($userInfo1));
    }

    public function testEqualsWithEmptyPasswordVsNoPassword(): void
    {
        $userInfo1 = new UserInfo('username', '');
        $userInfo2 = new UserInfo('username');

        self::assertFalse($userInfo1->equals($userInfo2));
        self::assertFalse($userInfo2->equals($userInfo1));
    }

    public function testEqualsWithEmptyPasswordVsNullPassword(): void
    {
        $userInfo1 = new UserInfo('username', '');
        $userInfo2 = new UserInfo('username', null);

        self::assertFalse($userInfo1->equals($userInfo2));
        self::assertFalse($userInfo2->equals($userInfo1));
    }

    public function testEqualsWithDifferentTypes(): void
    {
        $userInfo = new UserInfo('username', 'password');
        $other = 'not a user info';

        self::assertFalse($userInfo->equals($other));
    }

    public function testEqualsWithNull(): void
    {
        $userInfo = new UserInfo('username', 'password');

        self::assertFalse($userInfo->equals(null));
    }

    public function testEqualsWithCaseSensitiveUser(): void
    {
        $userInfo1 = new UserInfo('Username', 'password');
        $userInfo2 = new UserInfo('username', 'password');

        self::assertFalse($userInfo1->equals($userInfo2));
        self::assertFalse($userInfo2->equals($userInfo1));
    }

    public function testEqualsWithCaseSensitivePassword(): void
    {
        $userInfo1 = new UserInfo('username', 'Password');
        $userInfo2 = new UserInfo('username', 'password');

        self::assertFalse($userInfo1->equals($userInfo2));
        self::assertFalse($userInfo2->equals($userInfo1));
    }

    public function testImplementsUserInfoInterface(): void
    {
        $userInfo = new UserInfo('username', 'password');

        self::assertInstanceOf(UserInfoInterface::class, $userInfo);
    }

    public function testPropertiesAreReadonly(): void
    {
        $userInfo = new UserInfo('username', 'password');

        self::assertSame('username', $userInfo->username);
        self::assertSame('password', $userInfo->password);
    }

    public function testEdgeCaseEmptyUser(): void
    {
        $userInfo = new UserInfo('');

        self::assertSame('', $userInfo->username);
        self::assertNull($userInfo->password);
        self::assertSame('', (string) $userInfo);
    }

    public function testEdgeCaseEmptyUserWithPassword(): void
    {
        $userInfo = new UserInfo('', 'password');

        self::assertSame('', $userInfo->username);
        self::assertSame('password', $userInfo->password);
        self::assertSame(':password', (string) $userInfo);
    }

    public function testEdgeCaseUserWithColon(): void
    {
        $userInfo = new UserInfo('user:name', 'password');

        self::assertSame('user:name', $userInfo->username);
        self::assertSame('password', $userInfo->password);
        self::assertSame('user:name:password', (string) $userInfo);
    }

    public function testEdgeCasePasswordWithColon(): void
    {
        $userInfo = new UserInfo('username', 'pass:word');

        self::assertSame('username', $userInfo->username);
        self::assertSame('pass:word', $userInfo->password);
        self::assertSame('username:pass:word', (string) $userInfo);
    }

    public function testEdgeCaseUserWithAtSign(): void
    {
        $userInfo = new UserInfo('user@name', 'password');

        self::assertSame('user@name', $userInfo->username);
        self::assertSame('password', $userInfo->password);
        self::assertSame('user@name:password', (string) $userInfo);
    }

    public function testEdgeCasePasswordWithAtSign(): void
    {
        $userInfo = new UserInfo('username', 'pass@word');

        self::assertSame('username', $userInfo->username);
        self::assertSame('pass@word', $userInfo->password);
        self::assertSame('username:pass@word', (string) $userInfo);
    }

    public function testEdgeCaseUserWithSlash(): void
    {
        $userInfo = new UserInfo('user/name', 'password');

        self::assertSame('user/name', $userInfo->username);
        self::assertSame('password', $userInfo->password);
        self::assertSame('user/name:password', (string) $userInfo);
    }

    public function testEdgeCasePasswordWithSlash(): void
    {
        $userInfo = new UserInfo('username', 'pass/word');

        self::assertSame('username', $userInfo->username);
        self::assertSame('pass/word', $userInfo->password);
        self::assertSame('username:pass/word', (string) $userInfo);
    }

    public function testEdgeCaseUserWithQuestionMark(): void
    {
        $userInfo = new UserInfo('user?name', 'password');

        self::assertSame('user?name', $userInfo->username);
        self::assertSame('password', $userInfo->password);
        self::assertSame('user?name:password', (string) $userInfo);
    }

    public function testEdgeCasePasswordWithQuestionMark(): void
    {
        $userInfo = new UserInfo('username', 'pass?word');

        self::assertSame('username', $userInfo->username);
        self::assertSame('pass?word', $userInfo->password);
        self::assertSame('username:pass?word', (string) $userInfo);
    }

    public function testEdgeCaseUserWithHash(): void
    {
        $userInfo = new UserInfo('user#name', 'password');

        self::assertSame('user#name', $userInfo->username);
        self::assertSame('password', $userInfo->password);
        self::assertSame('user#name:password', (string) $userInfo);
    }

    public function testEdgeCasePasswordWithHash(): void
    {
        $userInfo = new UserInfo('username', 'pass#word');

        self::assertSame('username', $userInfo->username);
        self::assertSame('pass#word', $userInfo->password);
        self::assertSame('username:pass#word', (string) $userInfo);
    }

    public function testEdgeCaseUserWithSpace(): void
    {
        $userInfo = new UserInfo('user name', 'password');

        self::assertSame('user name', $userInfo->username);
        self::assertSame('password', $userInfo->password);
        self::assertSame('user name:password', (string) $userInfo);
    }

    public function testEdgeCasePasswordWithSpace(): void
    {
        $userInfo = new UserInfo('username', 'pass word');

        self::assertSame('username', $userInfo->username);
        self::assertSame('pass word', $userInfo->password);
        self::assertSame('username:pass word', (string) $userInfo);
    }

    public function testEdgeCaseUserWithUnicode(): void
    {
        $userInfo = new UserInfo('usér', 'password');

        self::assertSame('usér', $userInfo->username);
        self::assertSame('password', $userInfo->password);
        self::assertSame('usér:password', (string) $userInfo);
    }

    public function testEdgeCasePasswordWithUnicode(): void
    {
        $userInfo = new UserInfo('username', 'päss');

        self::assertSame('username', $userInfo->username);
        self::assertSame('päss', $userInfo->password);
        self::assertSame('username:päss', (string) $userInfo);
    }

    public function testEdgeCaseUserWithEmoji(): void
    {
        $userInfo = new UserInfo('user😀name', 'password');

        self::assertSame('user😀name', $userInfo->username);
        self::assertSame('password', $userInfo->password);
        self::assertSame('user😀name:password', (string) $userInfo);
    }

    public function testEdgeCasePasswordWithEmoji(): void
    {
        $userInfo = new UserInfo('username', 'pass😀word');

        self::assertSame('username', $userInfo->username);
        self::assertSame('pass😀word', $userInfo->password);
        self::assertSame('username:pass😀word', (string) $userInfo);
    }
}
