<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Configuration\Factory\JsonConfiguration;

use Boson\Component\Compiler\Configuration\Factory\JsonConfigurationFactory;
use Boson\Component\Compiler\Validator\FileValidatorInterface;
use Boson\Component\Compiler\Validator\JustInRainbowJsonSchemaValidator;

/**
 * @phpstan-import-type RawConfigurationType from JsonConfigurationFactory
 */
final readonly class JsonConfigValidator
{
    /**
     * @var non-empty-string
     */
    public const string JSON_SCHEMA_PATHNAME = __DIR__ . '/../../../../resources/boson.schema.json';

    /**
     * @var FileValidatorInterface<RawConfigurationType>
     */
    private FileValidatorInterface $validator;

    /**
     * @param non-empty-string $jsonSchemaPathname
     */
    public function __construct(
        string $jsonSchemaPathname = self::JSON_SCHEMA_PATHNAME,
    ) {
        /** @phpstan-ignore assign.propertyType */
        $this->validator = new JustInRainbowJsonSchemaValidator($jsonSchemaPathname);
    }

    /**
     * @phpstan-assert RawConfigurationType $data
     *
     * @param array<array-key, mixed> $data
     * @param non-empty-string $pathname
     */
    public function validateOrFail(array $data, string $pathname): void
    {
        $this->validator->validateOrFail($data, $pathname);
    }
}
