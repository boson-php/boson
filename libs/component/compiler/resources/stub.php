<?php

Phar::mapPhar('{name}.phar');

if (\is_file(__DIR__ . '/libboson-darwin-universal.dylib')) {
    Phar::mount('libboson-darwin-universal.dylib', __DIR__ . '/libboson-darwin-universal.dylib');
}

if (\is_file(__DIR__ . '/libboson-linux-aarch64.so')) {
    Phar::mount('libboson-linux-aarch64.so', __DIR__ . '/libboson-linux-aarch64.so');
}

if (\is_file(__DIR__ . '/libboson-linux-x86_64.so')) {
    Phar::mount('libboson-linux-x86_64.so', __DIR__ . '/libboson-linux-x86_64.so');
}

if (\is_file(__DIR__ . '/libboson-windows-x86_64.dll')) {
    Phar::mount('libboson-windows-x86_64.dll', __DIR__ . '/libboson-windows-x86_64.dll');
}

Phar::interceptFileFuncs();

require 'phar://{name}.phar/{entrypoint}';

__HALT_COMPILER();
