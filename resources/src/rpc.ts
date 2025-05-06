
import CryptoIdGenerator, {type IdGeneratorInterface} from "./id-generator";
import type {TransportInterface} from "./transport";

/**
 * Defer type for request promise instance
 */
type Deferred = {
    resolve: (result: any) => void;
    reject: (reason: Error) => void;
}

/**
 * RPC parameters list definition
 */
type BosonRpcParameters = Array<any> | {
    [parameter: string]: any
};


export interface BosonRpcResponderInterface {
    /**
     * Resolve deferred by its identifier.
     *
     * @param {number} id
     * @param {any} result
     */
    resolve(id: string, result: any): void;

    /**
     * Reject deferred by its identifier.
     *
     * @param {number} id
     * @param {Error} error
     */
    reject(id: string, error: Error): void;
}

export interface BosonRpcInterface {
    /**
     * Executes an external method.
     *
     * @param {string} method
     * @param {BosonRpcParameters} params
     */
    call(method: string, params: BosonRpcParameters): Promise<any>;
}

/**
 * An implementation of the RPC facade
 */
export default class BosonRpc implements BosonRpcInterface, BosonRpcResponderInterface {
    /**
     * List of sent RPC messages.
     *
     * @private
     */
    #messages: { [key: string]: Deferred } = {};

    /**
     * RPC message ID generator.
     *
     * @private
     */
    readonly #ids: IdGeneratorInterface;

    /**
     * RPC transport.
     *
     * @private
     */
    readonly #io: TransportInterface;

    /**
     * @param {TransportInterface} io RPC transport.
     * @param {IdGeneratorInterface} ids Optional ID generator instance.
     */
    constructor(
        io: TransportInterface,
        ids: IdGeneratorInterface = new CryptoIdGenerator(),
    ) {
        this.#io = io;
        this.#ids = ids;
    }

    /**
     * Get deferred from storage by its identifier.
     *
     * @param {string} id
     * @private
     */
    #fetch(id: string): Deferred|null {
        const deferred = this.#messages[id] ?? null;

        try {
            return deferred;
        } finally {
            if (deferred !== null) {
                delete this.#messages[id];
            }
        }
    }

    resolve(id: string, result: any): void {
        this.#fetch(id)?.resolve(result);
    }

    reject(id: string, error: Error): void {
        this.#fetch(id)?.reject(error);
    }

    /**
     * Creates a new promise for the given identifier.
     *
     * @param {string} id
     * @private
     */
    #createPromise(id: string): Promise<any> {
        return new Promise((resolve: (result: any) => void, reject: (reason?: any) => void): any =>
            this.#messages[id] = {resolve, reject}
        );
    }

    call(method: string, params: any): Promise<any> {
        const id = this.#ids.generate();
        const promise = this.#createPromise(id);

        this.#io.send(JSON.stringify({id, method, params}));

        return promise;
    }
}
