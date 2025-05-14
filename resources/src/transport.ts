
declare type PostMessageExecutor = (message: string) => void;

declare type Optional<T> = T | undefined;

declare const window: {
    /**
     * Saucer v6.0 API
     */
    saucer?: {
        internal?: {
            send_message?: PostMessageExecutor,
        },
    },
    /**
     * Chrome/Edge API
     */
    chrome?: {
        webview?: {
            postMessage?: PostMessageExecutor
        },
    }
};

export interface TransportInterface {
    /**
     * Send message string to the PHP runtime.
     *
     * @param {string} message
     */
    send(message: string): void;
}

abstract class PostMessageTransport implements TransportInterface {
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

export class ChromePostMessageTransport extends PostMessageTransport {
    static #findGlobalExecutor(): Optional<PostMessageExecutor> {
        return window.chrome?.webview?.postMessage;
    }

    static createFromGlobals(): ChromePostMessageTransport {
        return new ChromePostMessageTransport(this.#findGlobalExecutor());
    }

    static isSupported(): boolean {
        return this.#findGlobalExecutor() !== undefined;
    }
}

export class SaucerPostMessageTransport extends PostMessageTransport {
    static #findGlobalExecutor(): Optional<PostMessageExecutor> {
        return window.saucer?.internal?.send_message;
    }

    static createFromGlobals(): SaucerPostMessageTransport {
        return new SaucerPostMessageTransport(this.#findGlobalExecutor());
    }

    static isSupported(): boolean {
        return this.#findGlobalExecutor() !== undefined;
    }
}

export default class TransportFactory {
    static createFromGlobals(): TransportInterface {
        if (ChromePostMessageTransport.isSupported()) {
            return ChromePostMessageTransport.createFromGlobals();
        }

        if (SaucerPostMessageTransport.isSupported()) {
            return SaucerPostMessageTransport.createFromGlobals();
        }

        throw new Error('Can not select suitable transport');
    }
}

