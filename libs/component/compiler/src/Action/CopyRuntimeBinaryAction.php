<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Action;

use Boson\Component\Compiler\Configuration;
use Composer\InstalledVersions;

/**
 * @template-extends AssemblyAction<CopyRuntimeBinaryStatus>
 */
final readonly class CopyRuntimeBinaryAction extends AssemblyAction
{
    public function process(Configuration $config): iterable
    {
        yield CopyRuntimeBinaryStatus::ReadyToCopy;

        $sourceBinary = $this->getSourceRuntimeBinary();
        $targetBinary = $this->getTargetRuntimeBinary($config);

        if (!\is_readable($sourceBinary)) {
            throw new \RuntimeException(\sprintf(
                'Could not find runtime binary "%s"',
                $this->assembly->frontend,
            ));
        }

        if (!\is_file($targetBinary)) {
            \copy($sourceBinary, $targetBinary);
        }

        yield CopyRuntimeBinaryStatus::Copied;
    }

    /**
     * @return non-empty-string
     */
    private function getTargetRuntimeBinary(Configuration $config): string
    {
        return $this->assembly->getBuildDirectory($config)
            . \DIRECTORY_SEPARATOR . $this->assembly->frontend;
    }

    /**
     * @return non-empty-string
     */
    private function getSourceRuntimeBinary(): string
    {
        return InstalledVersions::getInstallPath('boson-php/runtime')
            . \DIRECTORY_SEPARATOR . 'bin'
            . \DIRECTORY_SEPARATOR . $this->assembly->frontend;
    }
}
