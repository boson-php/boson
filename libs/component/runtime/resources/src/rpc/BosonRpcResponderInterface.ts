import type {IdType} from "../id-generator/IdGeneratorInterface";

export default interface BosonRpcResponderInterface {
    /**
     * Resolve deferred by its identifier.
     */
    resolve(id: IdType, result: any): void;

    /**
     * Reject deferred by its identifier.
     */
    reject(id: IdType, error: Error): void;
}
