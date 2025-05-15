import type IdGeneratorInterface from "./IdGeneratorInterface";

/**
 * Default generated string length
 */
export const DEFAULT_LENGTH: number = 16;

export default abstract class StringIdGenerator implements IdGeneratorInterface<string> {
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
