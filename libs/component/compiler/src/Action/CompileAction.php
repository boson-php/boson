<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Action;

use Boson\Component\Compiler\Assembly\Assembly;
use Boson\Component\Compiler\Configuration;
use Boson\Component\OsInfo\Family;

/**
 * @template-extends AssemblyAction<CompileStatus>
 */
final readonly class CompileAction extends AssemblyAction
{
    public function process(Configuration $config): iterable
    {
        yield $this->assembly => CompileStatus::ReadyToCompile;

        $this->validatePharArchive($config);

        $targetPathname = $this->getBinaryTargetPathname($config);

        $targetStream = \fopen($targetPathname, 'wb+');
        \flock($targetStream, \LOCK_EX);

        $this->appendSfxArchive($targetStream);
        $this->appendPhpConfig($targetStream, $config);
        $this->appendSource($targetStream, $config);

        \flock($targetStream, \LOCK_UN);
        \fclose($targetStream);

        yield $this->assembly => CompileStatus::Compiled;
    }

    private function getBinaryTargetPathname(Configuration $config): string
    {
        $result = $this->assembly->getBuildDirectory($config)
            . \DIRECTORY_SEPARATOR . $config->name;

        if ($this->assembly->family->is(Family::Windows)) {
            $result .= '.exe';
        }

        return $result;
    }

    /**
     * @param resource $stream
     */
    private function appendSource(mixed $stream, Configuration $config): void
    {
        $sourceStream = \fopen($config->pharPathname, 'rb');
        \flock($sourceStream, \LOCK_SH);

        \stream_copy_to_stream($sourceStream, $stream);

        \fclose($sourceStream);
    }

    /**
     * @param resource $stream
     */
    private function appendPhpConfig(mixed $stream, Configuration $config): void
    {
        $ini = $this->getPhpConfigString($config);

        \fwrite($stream, "\xfd\xf6\x69\xe6");
        \fwrite($stream, \pack('N', \strlen($ini)));
        \fwrite($stream, $ini);
    }

    /**
     * @param resource $stream
     */
    private function appendSfxArchive(mixed $stream): void
    {
        $archive = $this->getSfxArchivePathname($this->assembly);

        $archiveStream = \fopen($archive, 'rb');
        \flock($archiveStream, \LOCK_SH);

        \stream_copy_to_stream($archiveStream, $stream);

        \fclose($archiveStream);
    }

    private function validatePharArchive(Configuration $config): void
    {
        if (\is_readable($config->pharPathname)) {
            return;
        }

        throw new \RuntimeException(\sprintf(
            'Application archive "%s" is not available',
            $config->pharPathname,
        ));
    }

    /**
     * @return non-empty-string
     */
    private function getPhpConfigString(Configuration $config): string
    {
        $ini = <<<'INI'
            ffi.enable=1
            opcache.enable=1
            INI;

        foreach ($config->ini as $key => $value) {
            $ini .= "\n$key=" . match ($value) {
                false => '0',
                true => '1',
                default => (string) $value,
            };
        }

        return $ini . "\n";
    }

    private function getSfxArchivePathname(Assembly $assembly): string
    {
        $result = __DIR__ . '/../../bin/' . $assembly->backend;

        $normalized = \realpath($result);

        if ($normalized === false) {
            throw new \RuntimeException(\sprintf(
                'Could not find SFX "%s" binary',
                $result,
            ));
        }

        return $normalized;
    }
}
