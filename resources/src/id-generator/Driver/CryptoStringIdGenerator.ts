import StringIdGenerator from "../StringIdGenerator";
import type {Optional} from "../../common/Optional";

declare const window: {
    crypto?: Crypto,
    msCrypto?: Crypto,
    msrCrypto?: Crypto,
};

export default class CryptoStringIdGenerator extends StringIdGenerator {
    #crypto: Crypto;

    constructor(crypto?: Crypto) {
        super();

        this.#crypto = crypto || (function () {
            throw new Error('Could not load client cryptographic library');
        })();
    }

    static #findGlobalGenerator(): Optional<Crypto> {
        return window.crypto || window.msCrypto || window.msrCrypto || undefined;
    }

    static createFromGlobals(): CryptoStringIdGenerator {
        return new CryptoStringIdGenerator(this.#findGlobalGenerator());
    }

    static isSupported(): boolean {
        return !!this.#findGlobalGenerator();
    }

    generateByteArray(length: number): Uint8Array {
        return this.#crypto.getRandomValues(new Uint8Array(length));
    }
}
