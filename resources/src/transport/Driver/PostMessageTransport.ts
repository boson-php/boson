import type {TransportInterface} from "../TransportInterface";

export type PostMessageExecutor = (message: string) => void;

export default abstract class PostMessageTransport implements TransportInterface {
    readonly #executor: PostMessageExecutor;

    constructor(executor?: PostMessageExecutor) {
        this.#executor = executor ?? (function () {
            throw new Error('Unsupported transport');
        })();
    }

    send(message: string): void {
        this.#executor(message);
    }
}
