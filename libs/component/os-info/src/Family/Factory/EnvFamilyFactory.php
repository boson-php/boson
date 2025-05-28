<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Family\Factory;

use Boson\Component\OsInfo\FamilyInterface;

final readonly class EnvFamilyFactory extends FamilyByNameFactory
{
    /**
     * @var non-empty-string
     */
    public const string DEFAULT_OVERRIDE_ENV_NAME = 'BOSON_OS_NAME';

    public function __construct(
        private FamilyFactoryInterface $delegate,
        /**
         * @var list<non-empty-string>
         */
        private array $envVariableNames = [],
    ) {}

    public static function createForOverrideEnvVariables(FamilyFactoryInterface $delegate): self
    {
        return new self($delegate, [
            self::DEFAULT_OVERRIDE_ENV_NAME,
        ]);
    }

    /**
     * @return non-empty-string|null
     */
    private function tryGetNameFromEnvironment(): ?string
    {
        foreach ($this->envVariableNames as $name) {
            $server = $_SERVER[$name] ?? null;

            if (\is_string($server) && $server !== '') {
                return $server;
            }
        }

        return null;
    }

    public function createFamily(): FamilyInterface
    {
        $name = $this->tryGetNameFromEnvironment();

        if ($name === null) {
            return $this->delegate->createFamily();
        }

        return $this->createFromName($name);
    }
}
