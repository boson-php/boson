<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Action;

enum DownloadBoxStatus
{
    case ReadyToDownload;
    case Downloading;
    case Complete;
}
