
import BosonRpc from "./rpc";
import TransportFactory, {type TransportInterface} from "./transport";
import CryptoIdGenerator, {type IdGeneratorInterface} from "./id-generator.ts";

export type BosonApi = {
    io: TransportInterface,
    ids: IdGeneratorInterface,
    rpc: BosonRpc,
    respond: (id: string, result: any) => void,
    components: {
        created?: (id: string, tag: string) => void,
        connected?: (id: string) => void,
        disconnected?: (id: string) => void,
        attributeChanged?: (id: string, attribute: string, value: any, previous: any) => void,
        instances: { [key: string]: HTMLElement }
    },
}

declare const window: {
    boson: BosonApi,
};

const ids = new CryptoIdGenerator();
const io = TransportFactory.createFromGlobals();
const rpc = new BosonRpc(io, ids);

/**
 * Prepare public accessor instance.
 */
window.boson = window.boson || {};
window.boson.io = io;
window.boson.ids = ids;
window.boson.rpc = rpc;
window.boson.components = {
    instances: {},
};
