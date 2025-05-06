
declare const window: {
    crypto?: Crypto,
    msCrypto?: Crypto,
    msrCrypto?: Crypto,
};

/**
 * Default generated string length
 */
export const DEFAULT_LENGTH: number = 16;

export interface IdGeneratorInterface {
    /**
     * Gets random hexadecimal string of expected
     * length (2 chars per byte).
     *
     * @param {number} length
     */
    generate(length?: number): string;
}

abstract class IdGenerator implements IdGeneratorInterface {
    /**
     * Gets random array of expected length.
     *
     * @param {number} length
     * @private
     */
    abstract generateByteArray(length: number): Uint8Array;

    /**
     * Convert uint8 byte to hexadecimal string (chars pair)
     *
     * @param {number} byte
     * @private
     */
    #toHexPair(byte: number): string {
        return byte
            .toString(16)
            .padStart(2, '0');
    }

    generate(length: number = DEFAULT_LENGTH): string {
        return Array.from(this.generateByteArray(length))
            .map(this.#toHexPair)
            .join('');
    }
}

export default class CryptoIdGenerator extends IdGenerator {
    #crypto: Crypto;

    constructor(crypto: Crypto|null = null) {
        super();

        this.#crypto = crypto || CryptoIdGenerator.#getCryptoFromGlobals();
    }

    /**
     * Gets crypto from global environment.
     *
     * @private
     */
    static #getCryptoFromGlobals(): Crypto {
        return window.crypto || window.msCrypto || window.msrCrypto || (function () {
            throw new Error('Could not load client cryptographic library');
        })();
    }

    generateByteArray(length: number): Uint8Array {
        return this.#crypto.getRandomValues(new Uint8Array(length));
    }
}
