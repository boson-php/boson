<?php

declare(strict_types=1);

namespace Boson;

use Boson\Window\WindowCreateInfo;

/**
 * Information (configuration) DTO for creating a new application.
 */
final readonly class ApplicationCreateInfo
{
    /**
     * Contains default application name.
     *
     * @var non-empty-string
     */
    public const string DEFAULT_APPLICATION_NAME = 'boson';

    public function __construct(
        /**
         * An application optional name.
         *
         * @var non-empty-string
         */
        public string $name = self::DEFAULT_APPLICATION_NAME,
        /**
         * An application threads count.
         *
         * The number of threads will be determined automatically if it
         * is not explicitly specified (defined as {@see null}).
         *
         * @var int<1, 32767>|null
         */
        public ?int $threads = null,
        /**
         * Automatically detects debug environment if {@see null},
         * otherwise it forcibly enables or disables it.
         */
        public ?bool $debug = null,
        /**
         * Automatically detects the library pathname if {@see null},
         * otherwise it forcibly exposes it.
         */
        public ?string $library = null,
        /**
         * Automatically terminates the application if
         * all windows have been closed.
         */
        public bool $quitOnClose = true,
        /**
         * Automatically starts the application if set to {@see true}.
         */
        public bool $autorun = true,
        /**
         * Main (default) window configuration DTO.
         */
        public WindowCreateInfo $window = new WindowCreateInfo(),
    ) {}
}
