<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Validator;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;

/**
 * @phpstan-type JsonSchemaErrorType array<array-key, array{
 *     message: non-empty-string,
 *     property: non-empty-string,
 *     constraint?: array{
 *         name?: string,
 *         ...
 *     },
 *     ...
 * }>
 *
 * @template TSchema of array<array-key, mixed>
 *
 * @template-implements FileValidatorInterface<TSchema>
 */
final readonly class JustInRainbowJsonSchemaValidator implements FileValidatorInterface
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        private string $jsonSchemaPathname,
    ) {}

    /**
     * @phpstan-assert TSchema $data
     *
     * @param array<array-key, mixed> $data
     * @param non-empty-string|null $pathname
     */
    public function validateOrFail(array $data, ?string $pathname = null): void
    {
        $validator = $this->validate($data);

        /** @var JsonSchemaErrorType $errors */
        $errors = $validator->getErrors();
        $message = $this->getFormattedErrorMessage($errors);

        if ($message === null) {
            return;
        }

        if ($pathname !== null) {
            $pathname = \sprintf(' in "%s"', \addcslashes($pathname, '"'));
        }

        throw new \InvalidArgumentException(\sprintf(
            "The following configuration errors were found%s: \n%s",
            $pathname,
            $message,
        ));
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function validate(array $data): Validator
    {
        $schema = \json_decode((string) \file_get_contents($this->jsonSchemaPathname));

        assert(\is_object($schema), 'Schema must be an object');

        $validator = new Validator();
        $validator->validate($data, $schema, Constraint::CHECK_MODE_TYPE_CAST);

        return $validator;
    }

    /**
     * @param JsonSchemaErrorType $errors
     *
     * @return non-empty-string|null
     */
    private function getFormattedErrorMessage(array $errors): ?string
    {
        $result = [];

        foreach ($this->filterErrorMessages($errors) as $localPath => $localMessages) {
            $realPath = \rtrim($localPath, '.');

            $result[] = \vsprintf("- An error at \"%s\":\n  └ %s", [
                $realPath,
                \implode("\n  └ ", $localMessages),
            ]);
        }

        if ($result === []) {
            return null;
        }

        return \implode("\n", $result);
    }

    /**
     * @param JsonSchemaErrorType $errors
     *
     * @return iterable<non-empty-string, list<non-empty-string>>
     */
    private function filterErrorMessages(array $errors): iterable
    {
        $processedPaths = [];

        foreach ($this->groupErrorMessages($errors) as $path => $messages) {
            if ($this->containsInPath($processedPaths, $path)) {
                continue;
            }

            yield $path => $messages;

            $processedPaths[] = $path;
        }
    }

    /**
     * @param list<non-empty-string> $processedPaths
     * @param non-empty-string $path
     */
    private function containsInPath(array $processedPaths, string $path): bool
    {
        foreach ($processedPaths as $processedPath) {
            if (\str_contains($processedPath, $path . '.')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param JsonSchemaErrorType $errors
     *
     * @return array<non-empty-string, list<non-empty-string>>
     */
    private function groupErrorMessages(array $errors): array
    {
        $groups = [];

        foreach ($errors as $error) {
            switch ($error['constraint']['name'] ?? null) {
                // Exclude "anyOf" constraints
                case 'anyOf':
                    continue 2;
                default:
                    $groups[$error['property']][] = $error['message'];
            }
        }

        return $groups;
    }
}
