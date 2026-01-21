<?php

declare(strict_types=1);

namespace Boson\Api\Dialog;

/**
 * Provides functionality for accessing OS files and directories
 */
interface DialogApiInterface extends
    FileOpenerInterface,
    FileSelectorInterface {}
