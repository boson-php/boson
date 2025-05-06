
import BosonRpc from "./rpc";
import TransportFactory, {type TransportInterface} from "./transport";

export type BosonApi = {
    io: TransportInterface,
    rpc: BosonRpc,
    respond: (id: string, result: any) => void,
}

declare const window: {
    boson: BosonApi,
};

const io = TransportFactory.createFromGlobals();
const rpc = new BosonRpc(io);

/**
 * Prepare public accessor instance.
 */
window.boson = window.boson || {};
window.boson.io = io;
window.boson.rpc = rpc;
