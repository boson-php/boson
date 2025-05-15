import type {TransportInterface} from "./TransportInterface";
import ChromePostMessageTransport from "./Driver/ChromePostMessageTransport";
import SaucerPostMessageTransport from "./Driver/SaucerPostMessageTransport";

export default class TransportFactory {
    static createFromGlobals(): TransportInterface {
        if (ChromePostMessageTransport.isSupported()) {
            return ChromePostMessageTransport.createFromGlobals();
        }

        if (SaucerPostMessageTransport.isSupported()) {
            return SaucerPostMessageTransport.createFromGlobals();
        }

        throw new Error('Can not select suitable IPC transport');
    }
}

