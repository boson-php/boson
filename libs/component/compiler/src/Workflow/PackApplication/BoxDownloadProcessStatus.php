<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow\PackApplication;

enum BoxDownloadProcessStatus
{
    case ReadyToDownload;
    case Downloading;
    case Complete;
}
