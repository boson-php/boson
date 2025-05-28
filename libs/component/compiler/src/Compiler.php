<?php

declare(strict_types=1);

namespace Boson\Component\Compiler;

use InvalidArgumentException;

final readonly class Compiler
{
    public const array PLATFORMS = [
        'linux' => ['x64_64', 'aarch64'],
        'macos' => ['x64_64', 'aarch64'],
        'windows' => ['x64_64'],
    ];

    public const array TYPES = [
        'minimal',
        'standard',
    ];

    public string $targetPath;

    public string $pharPath;

    public string $boxJsonPath;

    public Configuration $config;

    /**
     * @param  non-empty-string  $applicationPath
     * @param  non-empty-string  $targetDir
     * @param  non-empty-string  $tmpDir
     * @param  non-empty-string  $stubsPath
     */
    public function __construct(
        private string $applicationPath,
        private string $targetDir,
        private string $tmpDir,
        private string $stubsPath,
    ) {
        $this->initConfiguration();
        $this->initTargetPath();
        $this->initTmp();

        $this->pharPath = "$this->tmpDir/app.phar";
        $this->boxJsonPath = "$this->tmpDir/box.json";
    }

    private function initTmp(): void
    {
        if(!file_exists($this->tmpDir)) {
            @mkdir($this->tmpDir);
        }
    }

    private function initTargetPath(): void
    {
        !file_exists("$this->applicationPath/$this->targetDir")
        && @mkdir("$this->applicationPath/$this->targetDir");

        $this->targetPath = "$this->applicationPath/$this->targetDir/{$this->config->name}";
    }

    private function initConfiguration(): void
    {
        $configPath = "$this->applicationPath/boson.json";

        if(!file_exists($configPath)) {
            file_put_contents(
                $configPath,
                file_get_contents("$this->stubsPath/boson.json"),
            );
        }

        $this->config = Configuration::fromArray(
            json_decode(
                file_get_contents($configPath),
                true,
            ),
        );
    }

    private function toBoxJson(): string
    {
        return json_encode(array_filter([
            "base-path" => $this->applicationPath,
            "main" => $this->config->entrypoint,
            "output" => $this->pharPath,
            "chmod" => "0755",
            "compression" => "GZ",
            "exclude-composer-files" => true,
            "files" => $this->config->files,
            "directories" => $this->config->directories,
        ]));
    }

    private function validatePlatform(string $platform, string $arch, string $type): void
    {
        if(!isset(self::PLATFORMS[$platform])) {
            throw new InvalidArgumentException("Unknown platform - $platform");
        }

        if(!in_array($arch, self::PLATFORMS[$platform], true)) {
            throw new InvalidArgumentException("Unknown arch - $arch");
        }

        if(!in_array($type, self::TYPES, true)) {
            throw new InvalidArgumentException("Unknown type - $type");
        }
    }

    public function compile(string $platform, string $arch, string $type = 'minimal'): void
    {
        $this->validatePlatform($platform, $arch, $type);

        $frontend = "libboson-$platform-$arch.dll";

        if($platform === 'macos') {
            $frontend = 'libboson-darwin-universal.dylib';
        }

        \copy(
            "$this->applicationPath/vendor/boson-php/runtime/bin/$frontend",
            dirname($this->targetPath) . "/$frontend",
        );

        file_put_contents(
            $this->tmpDir . '/box.json',
            $this->toBoxJson(),
        );

        $microPath = __DIR__ . "/../php-bin/$type/$platform-$arch.sfx";

        if(!file_exists("$this->applicationPath/vendor/bin/box")) {
            \passthru("cd $this->applicationPath && composer bin box require --dev humbug/box");
        }

        \passthru("cd $this->applicationPath && vendor/bin/box compile --config=$this->boxJsonPath");

        $ini = <<<'INI'
            ffi.enable=1
            opcache.enable=1
            opcache.jit_buffer_size=128M
            INI;

        $output = fopen($this->targetPath, 'wb+');
        stream_copy_to_stream(fopen($microPath, 'rb'), $output);

        fwrite($output, "\xfd\xf6\x69\xe6");
        fwrite($output, pack('N', strlen($ini)));
        fwrite($output, $ini);
        fwrite($output, file_get_contents($this->pharPath));
        fclose($output);

        unlink($this->pharPath);
        unlink($this->boxJsonPath);
    }
}
