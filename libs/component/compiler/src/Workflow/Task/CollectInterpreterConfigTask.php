<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow\Task;

use Boson\Component\Compiler\Configuration;
use Boson\Component\Compiler\Target\TargetInterface;
use Boson\Component\Compiler\Workflow\Task;

/**
 * @template-implements TaskInterface<non-empty-string>
 */
final readonly class CollectInterpreterConfigTask implements TaskInterface
{
    /**
     * @var non-empty-string
     */
    private const string DEFAULT_INI_CONFIG = <<<'INI'
        ffi.enable=1
        INI;

    public function __construct(
        public TargetInterface $target,
    ) {}

    /**
     * @return iterable<non-empty-string, scalar>
     */
    private function getInterpreterConfigAsIterator(Configuration $config): iterable
    {
        yield from $config->ini;

        if (isset($this->target->config['ini']) && \is_array($this->target->config['ini'])) {
            foreach ($this->target->config['ini'] as $key => $value) {
                if (!\is_string($key) || $key === '' || !\is_scalar($value)) {
                    continue;
                }

                yield $key => $value;
            }
        }
    }

    public function __invoke(Configuration $config): string
    {
        Task::info('Build interpreter target configuration');

        $ini = self::DEFAULT_INI_CONFIG;

        foreach ($this->getInterpreterConfigAsIterator($config) as $key => $value) {
            Task::notify('Add [%s = %s]', [
                $key,
                $value,
            ]);

            $ini .= "\n$key=" . match ($value) {
                false => '0',
                true => '1',
                default => (string) $value,
            };
        }

        return $ini;
    }
}
