<?php

declare(strict_types=1);

namespace Boson;

use Boson\Http\Middleware\MiddlewareInterface;
use Boson\Window\WindowCreateInfo;

/**
 * Information (configuration) DTO for creating a new application.
 *
 * @phpstan-type SchemeNamesType iterable<mixed, non-empty-string>
 * @phpstan-type SchemeMiddlewareType iterable<mixed, MiddlewareInterface>
 * @phpstan-type SchemeNameAndMiddlewareType iterable<non-empty-string, SchemeMiddlewareType>
 */
final readonly class ApplicationCreateInfo
{
    /**
     * Contains default application name.
     *
     * @var non-empty-string
     */
    public const string DEFAULT_APPLICATION_NAME = 'boson';

    /**
     * List of protocol (scheme) names that will be
     * intercepted by the application.
     *
     * @var array<non-empty-lowercase-string, list<MiddlewareInterface>>
     */
    public array $schemes;

    /**
     * @param SchemeNamesType|SchemeNameAndMiddlewareType $schemes list of scheme names
     */
    public function __construct(
        /**
         * An application optional name.
         *
         * @var non-empty-string
         */
        public string $name = self::DEFAULT_APPLICATION_NAME,
        iterable $schemes = [],
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
    ) {
        $this->schemes = self::formatSchemes($schemes);
    }

    /**
     * @param SchemeNamesType|SchemeNameAndMiddlewareType $schemes
     *
     * @return array<non-empty-lowercase-string, list<MiddlewareInterface>>
     */
    private static function formatSchemes(iterable $schemes): array
    {
        $result = [];

        foreach ($schemes as $scheme => $middleware) {
            // Matches ['scheme' => [middleware-1, middleware-2]] format
            if (\is_string($scheme) && $scheme !== '' && \is_iterable($middleware)) {
                $result[\strtolower($scheme)] = \iterator_to_array($middleware, false);
                // Matches ['scheme'] format
            } elseif (\is_string($middleware) && $middleware !== '') {
                $result[\strtolower($middleware)] = [];
            } else {
                throw new \InvalidArgumentException('Invalid scheme definition');
            }
        }

        return $result;
    }
}
