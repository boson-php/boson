<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Configuration\Factory;

use Boson\Component\Compiler\Configuration;
use Boson\Component\Compiler\Configuration\DirectoryIncludeConfiguration;
use Boson\Component\Compiler\Configuration\FileIncludeConfiguration;
use Boson\Component\Compiler\Configuration\FinderIncludeConfiguration;
use Boson\Component\Compiler\Configuration\IncludeConfiguration;
use JsonSchema\Validator;

/**
 * @phpstan-type RawFinderInclusionType object{
 *     directory: non-empty-string,
 *     name: non-empty-string
 * }
 * @phpstan-type RawFileInclusionType non-empty-string
 * @phpstan-type RawDirectoryInclusionType non-empty-string
 * @phpstan-type RawConfigurationType object{
 *     name?: non-empty-string,
 *     entrypoint?: non-empty-string,
 *     output?: non-empty-string,
 *     root?: non-empty-string,
 *     box-version?: non-empty-string,
 *     build?: object{
 *         files: list<RawFileInclusionType>,
 *         directories: list<RawDirectoryInclusionType>,
 *         finder: list<RawFinderInclusionType>
 *     },
 *     ini?: object
 * }
 */
final class JsonConfigurationFactory implements ConfigurationFactoryInterface
{
    /**
     * @var non-empty-string
     */
    public const string DEFAULT_JSON_FILENAME = 'boson.json';

    /**
     * @var non-empty-string
     */
    private const string JSON_SCHEMA_FILENAME = __DIR__ . '/../../../resources/boson.schema.json';

    /**
     * @param non-empty-string $filename
     */
    public function __construct(
        private readonly string $filename = self::DEFAULT_JSON_FILENAME,
    ) {}

    /**
     * @return non-empty-string
     */
    private function readSchemaAsJsonString(): string
    {
        $result = @\file_get_contents(self::JSON_SCHEMA_FILENAME);

        if ($result === false || $result === '') {
            throw new \RuntimeException('Failed to load configuration schema file');
        }

        return $result;
    }

    /**
     * @throws \JsonException
     */
    private function readSchemaAsObject(): object
    {
        $json = $this->readSchemaAsJsonString();

        return (object) \json_decode($json, false, 64, \JSON_THROW_ON_ERROR);
    }

    private function validate(mixed $config): Validator
    {
        $validator = new Validator();

        try {
            $schema = $this->readSchemaAsObject();
        } catch (\Throwable) {
            throw new \RuntimeException('An error occurred while parsing configuration schema file');
        }

        $validator->validate($config, $schema);

        return $validator;
    }

    private function readConfigAsJsonStringFromReadable(string $pathname): ?string
    {
        $contents = @\file_get_contents($pathname);

        if ($contents === false) {
            return null;
        }

        return $contents;
    }

    private function readConfigAsJsonString(Configuration $config): ?string
    {
        if (\is_readable($pathname = $config->root . '/' . $this->filename)) {
            return $this->readConfigAsJsonStringFromReadable($pathname);
        }

        if (\is_readable($pathname = $this->filename)) {
            return $this->readConfigAsJsonStringFromReadable($pathname);
        }

        return null;
    }

    /**
     * @throws \JsonException
     */
    private function readConfigAsObject(Configuration $config): ?object
    {
        $json = $this->readConfigAsJsonString($config);

        if ($json === null) {
            return null;
        }

        return (object) \json_decode($json, false, 64, \JSON_THROW_ON_ERROR);
    }

    private function validateConfigOrFail(object $data): void
    {
        $validator = $this->validate($data);

        /** @var array{property: non-empty-string, message: non-empty-string, ...} $error */
        foreach ($validator->getErrors() as $error) {
            throw new \RuntimeException(\vsprintf("%s in $.%s\nin config %s", [
                $error['message'],
                $error['property'],
                /** @phpstan-ignore ternary.shortNotAllowed */
                \realpath($this->filename) ?: $this->filename,
            ]));
        }
    }

    /**
     * @return RawConfigurationType|null
     */
    private function loadConfigOrFail(Configuration $config): ?object
    {
        try {
            return $this->readConfigAsObject($config);
        } catch (\Throwable $e) {
            throw new \RuntimeException(\sprintf(
                '%s: An error occurred while parsing "%s" configuration file',
                $e->getMessage(),
                /** @phpstan-ignore ternary.shortNotAllowed */
                \realpath($this->filename) ?: $this->filename,
            ));
        }
    }

    public function createConfiguration(Configuration $config): Configuration
    {
        $data = $this->loadConfigOrFail($config);

        if ($data === null) {
            return $config;
        }

        $this->validateConfigOrFail($data);

        if (\property_exists($data, 'name')) {
            $config = $config->withName($data->name);
        }

        if (\property_exists($data, 'entrypoint')) {
            $config = $config->withEntrypoint($data->entrypoint);
        }

        if (\property_exists($data, 'box-version')) {
            /** @phpstan-ignore-next-line */
            $config = $config->withBoxVersion($data->{'box-version'});
        }

        if (\property_exists($data, 'output')) {
            $config = $config->withOutputDirectory($data->output);
        }

        if (\property_exists($data, 'root')) {
            $root = $data->root;

            if (\is_dir($root)) {
                /** @var non-empty-string $root */
                $root = (string) @\realpath($root);
            }

            $config = $config->withRootDirectory($root);
        } else {
            /** @var non-empty-string $root */
            $root = \dirname((string) \realpath($this->filename));

            $config = $config->withRootDirectory($root);
        }

        if (\property_exists($data, 'build')) {
            if (\property_exists($data->build, 'files')) {
                foreach ($data->build->files as $fileInclusion) {
                    $config = $config->withAddedBuildInclusion(
                        config: $this->createFileInclusion($fileInclusion),
                    );
                }
            }

            if (\property_exists($data->build, 'directories')) {
                foreach ($data->build->directories as $directoryInclusion) {
                    $config = $config->withAddedBuildInclusion(
                        config: $this->createDirectoryInclusion($directoryInclusion),
                    );
                }
            }

            if (\property_exists($data->build, 'finder')) {
                foreach ($data->build->finder as $finder) {
                    $inclusion = $this->createFinderInclusion($finder);

                    if ($inclusion !== null) {
                        $config = $config->withAddedBuildInclusion($inclusion);
                    }
                }
            }
        }

        if (\property_exists($data, 'ini')) {
            /**
             * @var non-empty-string $iniConfig
             * @var scalar $iniValue
             */
            foreach ((array) $data->ini as $iniConfig => $iniValue) {
                $config = $config->withAddedIni($iniConfig, $iniValue);
            }
        }

        return $config;
    }

    /**
     * @param RawFinderInclusionType $inclusion
     */
    private function createFinderInclusion(object $inclusion): ?IncludeConfiguration
    {
        $directory = $inclusion->directory ?? null;
        $name = $inclusion->name ?? null;

        if ($directory === null && $name === null) {
            return null;
        }

        return new FinderIncludeConfiguration(
            directory: $directory,
            name: $name,
        );
    }

    /**
     * @param non-empty-string $inclusion
     */
    private function createFileInclusion(string $inclusion): FileIncludeConfiguration
    {
        return new FileIncludeConfiguration($inclusion);
    }

    /**
     * @param non-empty-string $inclusion
     */
    private function createDirectoryInclusion(string $inclusion): DirectoryIncludeConfiguration
    {
        return new DirectoryIncludeConfiguration($inclusion);
    }
}
