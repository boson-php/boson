<?php

declare(strict_types=1);

namespace Boson\Http\Uri;

use Boson\Http\Uri\UserInfo\UserInfoValueObjectImpl;

final readonly class UserInfo implements UserInfoInterface
{
    use UserInfoValueObjectImpl;

    public function __construct(
        /**
         * @var non-empty-string
         */
        public string $user,
        /**
         * @var non-empty-string|null
         */
        #[\SensitiveParameter]
        public ?string $password = null,
    ) {}
}
