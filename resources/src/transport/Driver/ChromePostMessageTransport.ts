import PostMessageTransport, {type PostMessageExecutor} from "./PostMessageTransport";
import type {Optional} from "../../common/Optional";

declare const window: {
    /**
     * Chrome/Edge API
     */
    chrome?: {
        webview?: {
            postMessage?: PostMessageExecutor
        },
    }
};

export default class ChromePostMessageTransport extends PostMessageTransport {
    static #findGlobalExecutor(): Optional<PostMessageExecutor> {
        return window.chrome?.webview?.postMessage;
    }

    static createFromGlobals(): ChromePostMessageTransport {
        return new ChromePostMessageTransport(this.#findGlobalExecutor());
    }

    static isSupported(): boolean {
        return !!this.#findGlobalExecutor();
    }
}
