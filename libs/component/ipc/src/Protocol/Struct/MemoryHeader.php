<?php

declare(strict_types=1);

namespace Boson\Component\Ipc\Protocol\Struct;

final class MemoryHeader
{
    public const int BOSON_HEADER_SIZE = 6;

    public const int BOSON_IPC_MAGIK = 0xBABE;

    public const int BOSON_IPC_VERSION = 1;

    public bool $isValid {
        get => $this->magik === self::BOSON_IPC_MAGIK;
    }

    public bool $isEmpty {
        get => $this->magik === 0
            && $this->version === 0;
    }

    public function __construct(
        /**
         * uint32
         *
         * @var int<0, 4294967295>
         */
        public readonly int $magik = 0,
        /**
         * uint16
         *
         * @var int<0, 65535>
         */
        public readonly int $version = 0,
    ) {}

    public static function fromBytes(string $data): self
    {
        return new self(
            ...unpack('Lmagik/Sversion', $data),
        );
    }

    public static function createDefault(): self
    {
        return new self(
            magik: self::BOSON_IPC_MAGIK,
            version: self::BOSON_IPC_VERSION,
        );
    }

    public function __toString(): string
    {
        return \pack('LS', $this->magik, $this->version);
    }
}
