<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Assembly\Edition;

const MINIMAL = new BuiltinEdition('minimal', [
    'ffi',
    'phar',
    'opcache',
    'iconv',
    'mbstring',
    'ctype',
    'shmop',
]);

const STANDARD = new BuiltinEdition('standard', [
    'ffi',
    'phar',
    'opcache',
    'iconv',
    'mbstring',
    'ctype',
    'shmop',
    'sockets',
    'pdo_sqlite',
    'sqlite3',
    'intl',
    'curl',
    'dom',
    'xml',
    'sodium',
]);
