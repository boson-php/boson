<?php

declare(strict_types=1);

namespace Boson\Component\Saucer;

final readonly class PermissionType
{
    public const int SAUCER_PERMISSION_TYPE_UNKNOWN       = 0;
    public const int SAUCER_PERMISSION_TYPE_AUDIO_MEDIA   = 1 << 0;
    public const int SAUCER_PERMISSION_TYPE_VIDEO_MEDIA   = 1 << 1;
    public const int SAUCER_PERMISSION_TYPE_DESKTOP_MEDIA = 1 << 2;
    public const int SAUCER_PERMISSION_TYPE_MOUSE_LOCK    = 1 << 3;
    public const int SAUCER_PERMISSION_TYPE_DEVICE_INFO   = 1 << 4;
    public const int SAUCER_PERMISSION_TYPE_LOCATION      = 1 << 5;
    public const int SAUCER_PERMISSION_TYPE_CLIPBOARD     = 1 << 6;
    public const int SAUCER_PERMISSION_TYPE_NOTIFICATION  = 1 << 7;

    private function __construct() {}
}
