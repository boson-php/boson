<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow\CompileApplication;

use Boson\Component\Compiler\Assembly\Assembly;
use Boson\Component\Compiler\Configuration;

final readonly class CompileApplicationProcess
{
    public function process(Configuration $config, Assembly $assembly): iterable
    {
        yield $assembly => CompileApplicationProcessStatus::CompilationStarting;

        $targetBinary = $this->getBinary($assembly);

        if (!\is_readable($config->pharPathname)) {
            throw new \RuntimeException(\sprintf(
                'Application "%s" is not available',
                $config->pharPathname,
            ));
        }

        $targetPathname = $assembly->getBuildBinaryPathname($config);
        $targetStream = \fopen($targetPathname, 'wb+');
        \flock($targetStream, \LOCK_EX);

        \stream_copy_to_stream(
            from: \fopen($targetBinary, 'rb'),
            to: $targetStream,
        );

        $ini = $this->getIniString($config);

        \fwrite($targetStream, "\xfd\xf6\x69\xe6");
        \fwrite($targetStream, \pack('N', \strlen($ini)));
        \fwrite($targetStream, $ini);
        \stream_copy_to_stream(
            from: \fopen($config->pharPathname, 'rb'),
            to: $targetStream,
        );
        \fclose($targetStream);

        yield $assembly => CompileApplicationProcessStatus::CompilationCompleted;
    }

    private function getIniString(Configuration $config): string
    {
        $ini = <<<'INI'
            ffi.enable=1
            opcache.enable=1
            INI;

        foreach ($config->ini as $key => $value) {
            $ini .= "\n$key=" . match (true) {
                false => '0',
                true => '1',
                default => (string) $value,
            };
        }

        return $ini;
    }

    private function getBinary(Assembly $assembly): string
    {
        $result = __DIR__ . '/../../../bin/' . $assembly->backend;

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
