import CryptoStringIdGenerator from "./Driver/CryptoStringIdGenerator";
import type IdGeneratorInterface from "./IdGeneratorInterface";
import type {IdType} from "./IdGeneratorInterface";

export default class IdGeneratorFactory {
    static createFromGlobals(): IdGeneratorInterface<IdType> {
        if (CryptoStringIdGenerator.isSupported()) {
            return CryptoStringIdGenerator.createFromGlobals();
        }

        throw new Error('Can not select suitable ID generator');
    }
}
