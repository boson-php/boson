<?php

declare(strict_types=1);

namespace Boson;

use Boson\Api\Autorun\AutorunExtension;
use Boson\Api\CentralProcessor\CentralProcessorExtension;
use Boson\Api\Console\ConsoleExtension;
use Boson\Api\Dialog\DialogExtension;
use Boson\Api\OperatingSystem\OperatingSystemExtension;
use Boson\Api\QuitOnClose\QuitOnCloseExtension;
use Boson\Api\QuitSignals\QuitSignalsExtension;
use Boson\Extension\ExtensionInterface;
use Boson\Window\WindowCreateInfo;

//
// Note:
// 1) This "$_" assign hack removes these constants from IDE autocomplete.
// 2) Only define-like constants allows object instances.
//
\define($_ = 'Boson\DEFAULT_APPLICATION_EXTENSIONS', [
    new CentralProcessorExtension(),
    new OperatingSystemExtension(),
    new DialogExtension(),
    new QuitOnCloseExtension(),
    new ConsoleExtension(),
    new QuitSignalsExtension(),
    new AutorunExtension(),
]);

/**
 * Information (configuration) DTO for creating a new application.
 */
final readonly class ApplicationCreateInfo
{
    /**
     * @var list<ExtensionInterface<Application>>
     *
     * @noinspection PhpUndefinedConstantInspection
     */
    public const array DEFAULT_APPLICATION_EXTENSIONS = DEFAULT_APPLICATION_EXTENSIONS;

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
     * @var list<non-empty-lowercase-string>
     */
    public array $schemes;

    /**
     * @var list<ExtensionInterface<Application>>
     */
    public array $extensions;

    /**
     * @param iterable<mixed, non-empty-string> $schemes list of scheme names
     * @param iterable<mixed, ExtensionInterface<Application>> $extensions
     *        list of enabled application extensions
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
         * Automatically detects debug environment if {@see null},
         * otherwise it forcibly enables or disables it.
         */
        public ?bool $debug = null,
        /**
         * Automatically detects the library pathname if {@see null},
         * otherwise it forcibly exposes it.
         *
         * @var non-empty-string|null
         */
        public ?string $library = null,
        /**
         * Automatically terminates the application if
         * all windows have been closed.
         *
         * @deprecated will be removed in future versions it and replaced by
         *             the presence of the {@see QuitOnCloseExtension}
         *             in the {@see $extensions} list.
         *
         *             To disable this functionality, you should remove the
         *             {@see QuitOnCloseExtension} from the
         *             {@see $extensions} list, instead of setting the field
         *             to {@see false}.
         */
        public bool $quitOnClose = true,
        /**
         * Automatically starts the application if set to {@see true}.
         *
         * @deprecated will be removed in future versions it and replaced by
         *             the presence of the {@see AutorunExtension}
         *             in the {@see $extensions} list.
         *
         *             To disable this functionality, you should remove the
         *             {@see AutorunExtension} from the
         *             {@see $extensions} list, instead of setting the field
         *             to {@see false}.
         */
        public bool $autorun = true,
        iterable $extensions = self::DEFAULT_APPLICATION_EXTENSIONS,
        /**
         * Main (default) window configuration DTO.
         */
        public WindowCreateInfo $window = new WindowCreateInfo(),
    ) {
        $this->schemes = self::schemesToList($schemes);
        $this->extensions = self::extensionsToList($extensions);
    }

    /**
     * @param iterable<mixed, ExtensionInterface<Application>> $extensions
     *
     * @return list<ExtensionInterface<Application>>
     */
    private static function extensionsToList(iterable $extensions): array
    {
        return \iterator_to_array($extensions, false);
    }

    /**
     * @param iterable<mixed, non-empty-string> $schemes
     *
     * @return list<non-empty-lowercase-string>
     */
    private static function schemesToList(iterable $schemes): array
    {
        $result = [];

        foreach ($schemes as $scheme) {
            $result[] = \strtolower($scheme);
        }

        return $result;
    }

    /**
     * @param list<ExtensionInterface<Application>> $with
     * @param list<class-string<ExtensionInterface<Application>>> $except
     *
     * @return iterable<array-key, ExtensionInterface<Application>>
     */
    public static function extensions(array $with = [], array $except = []): iterable
    {
        /**
         * @var ExtensionInterface<Application> $extension
         *
         * @phpstan-ignore-next-line PHPStan does not support this constant
         */
        foreach (self::DEFAULT_APPLICATION_EXTENSIONS as $extension) {
            if (\in_array($extension::class, $except, true)) {
                continue;
            }

            yield $extension;
        }

        yield from $with;
    }
}
