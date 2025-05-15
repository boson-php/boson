import type {Optional} from "../common/Optional";

export default class BosonWebComponentsSet {
    #instances: { [key: string]: WeakRef<HTMLElement> } = {};

    get(id: string): Optional<HTMLElement> {
        let ref: Optional<WeakRef<HTMLElement>> = this.#instances[id];

        if (ref instanceof WeakRef) {
            let component: Optional<HTMLElement> = ref.deref();

            if (component instanceof HTMLElement) {
                return component;
            }

            delete this.#instances[id];
        }
    }

    detach(id: string): void {
        if (!!this.#instances[id]) {
            delete this.#instances[id];
        }
    }

    attach(id: string, el: HTMLElement) {
        if (!!this.#instances[id] && this.#instances[id].deref() === el) {
            return;
        }

        this.#instances[id] = new WeakRef(el);
    }
}
