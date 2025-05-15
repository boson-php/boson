import PostMessageTransport, { type PostMessageExecutor } from "./PostMessageTransport";
import type {Optional} from "../../common/Optional";

declare const window: {
    /**
     * Saucer v6.0 API
     */
    saucer?: {
        internal?: {
            send_message?: PostMessageExecutor,
        },
    },
};

export default class SaucerPostMessageTransport extends PostMessageTransport {
    static #findGlobalExecutor(): Optional<PostMessageExecutor> {
        return window.saucer?.internal?.send_message;
    }

    static createFromGlobals(): SaucerPostMessageTransport {
        return new SaucerPostMessageTransport(this.#findGlobalExecutor());
    }

    static isSupported(): boolean {
        return !!this.#findGlobalExecutor();
    }
}
