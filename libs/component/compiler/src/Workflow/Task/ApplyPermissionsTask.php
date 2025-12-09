<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow\Task;

use Boson\Component\Compiler\Configuration;
use Boson\Component\Compiler\Workflow\Task;
use Boson\Component\Compiler\Workflow\Task\Support\Path;

final readonly class ApplyPermissionsTask implements TaskInterface
{
    /**
     * @var non-empty-string
     */
    public string $pathname;

    /**
     * @var int<0, max>
     */
    public const int DEFAULT_WRITE_PERMISSIONS = 0o755;

    /**
     * @var int<0, max>
     */
    public const int DEFAULT_EXECUTE_PERMISSIONS = 0o777;

    /**
     * @param non-empty-string $pathname
     */
    public function __construct(
        string $pathname,
        /**
         * @var int<0, max>
         */
        public int $permissions = self::DEFAULT_WRITE_PERMISSIONS,
    ) {
        $this->pathname = Path::normalize($pathname);
    }

    /**
     * @return non-empty-string
     */
    private function permissionsToString(): string
    {
        $permissions = \sprintf('%o', $this->permissions);
        $permissions = \str_pad($permissions, 3, '0', \STR_PAD_LEFT);

        return match ($this->permissions) {
            self::DEFAULT_EXECUTE_PERMISSIONS => 'execute',
            self::DEFAULT_WRITE_PERMISSIONS => 'write',
            default => '0o' . $permissions,
        };
    }

    private function applyPermissions(): void
    {
        $isChanged = @\chmod($this->pathname, $this->permissions);

        if ($isChanged) {
            return;
        }

        throw new \RuntimeException(\sprintf(
            'Could not apply %s permissions to "%s"',
            $this->permissionsToString(),
            $this->pathname,
        ));
    }

    public function __invoke(Configuration $config): void
    {
        Task::info('Apply %s permissions to "%s"', [
            $this->permissionsToString(),
            Path::simplify($config, $this->pathname),
        ]);

        $this->applyPermissions();

        Task::notify('Applied %s permissions', [
            $this->permissionsToString(),
        ]);
    }
}
