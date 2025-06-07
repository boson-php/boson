<?php

declare(strict_types=1);

namespace Boson\Api;

use Boson\Api\Dialog\DirectorySelectorInterface;
use Boson\Api\Dialog\FileOpenerInterface;
use Boson\Api\Dialog\FileSelectorInterface;

interface DialogApiInterface extends
    FileOpenerInterface,
    FileSelectorInterface,
    DirectorySelectorInterface {}
