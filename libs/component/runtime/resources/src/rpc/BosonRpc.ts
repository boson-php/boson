
import type IdGeneratorInterface from "./../id-generator/IdGeneratorInterface";
import type {IdType} from "./../id-generator/IdGeneratorInterface";
import type {TransportInterface} from "./../transport/TransportInterface";
import type BosonRpcInterface from "./BosonRpcInterface";
import type BosonRpcResponderInterface from "./BosonRpcResponderInterface";

/**
 * Defer type for request promise instance
 */
type Deferred = {
    resolve: (result: any) => void;
    reject: (reason: Error) => void;
}

/**
 * An implementation of the RPC facade
 */
export default class BosonRpc<T extends IdType> implements
    BosonRpcInterface,
    BosonRpcResponderInterface
{
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
    readonly #ids: IdGeneratorInterface<T>;

    /**
     * RPC transport.
     *
     * @private
     */
    readonly #io: TransportInterface;

    constructor(io: TransportInterface, ids: IdGeneratorInterface<T>) {
        this.#io = io;
        this.#ids = ids;
    }

    /**
     * Get deferred from storage by its identifier.
     *
     * @private
     */
    #fetch(id: T): Deferred|null {
        const deferred = this.#messages[id] ?? null;

        try {
            return deferred;
        } finally {
            if (deferred !== null) {
                delete this.#messages[id];
            }
        }
    }

    resolve(id: T, result: any): void {
        if (result instanceof Promise) {
            result.then(successful => this.resolve(id, successful))
                .catch(rejection => this.reject(id, rejection));
        } else {
            this.#fetch(id)?.resolve(result);
        }
    }

    reject(id: T, error: Error): void {
        this.#fetch(id)?.reject(error);
    }

    /**
     * Creates a new promise for the given identifier.
     *
     * @private
     */
    #createPromiseById(id: T): Promise<any> {
        return new Promise((resolve: (result: any) => void, reject: (reason?: any) => void): any =>
            this.#messages[id] = {resolve, reject}
        );
    }

    call(method: string, params: any): Promise<any> {
        const id = this.#ids.generate();
        const promise = this.#createPromiseById(id);

        this.#io.send(JSON.stringify({id, method, params}));

        return promise;
    }
}
