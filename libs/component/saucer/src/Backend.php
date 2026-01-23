<?php

declare(strict_types=1);

namespace Boson\Component\Saucer;

final readonly class Backend
{
    public const int BOSON_BACKEND_WEBVIEW2 = 0;
    public const int BOSON_BACKEND_WEBKIT = 1;
    public const int BOSON_BACKEND_WEBKIT_GTK = 2;
    public const int BOSON_BACKEND_QT = 3;
    public const int BOSON_BACKEND_UNKNOWN = -1;

    public const int BOSON_BACKEND_LAST = self::BOSON_BACKEND_QT;

    private function __construct() {}
}
