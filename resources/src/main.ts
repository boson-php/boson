
import {type BosonWebComponents} from "./components/BosonWebComponents";
import BosonWebComponentsSet from "./components/BosonWebComponentsSet";

import type {BosonDataResponder} from "./data/BosonDataResponder";

import type IdGeneratorInterface from "./id-generator/IdGeneratorInterface";
import type {IdType} from "./id-generator/IdGeneratorInterface";
import IdGeneratorFactory from "./id-generator/IdGeneratorFactory";

import BosonRpc from "./rpc/BosonRpc";

import type {TransportInterface} from "./transport/TransportInterface";
import TransportFactory from "./transport/TransportFactory";

export type BosonClientApi = {
    io: TransportInterface,
    ids: IdGeneratorInterface<IdType>,
    rpc: BosonRpc<IdType>,
    respond: BosonDataResponder<IdType>,
    components: BosonWebComponents,
}

declare const window: {
    boson: BosonClientApi,
};

const ids: IdGeneratorInterface<IdType> = IdGeneratorFactory.createFromGlobals();
const io: TransportInterface = TransportFactory.createFromGlobals();
const rpc = new BosonRpc(io, ids);

/**
 * Prepare public accessor instance.
 */
window.boson = window.boson || {};
window.boson.io = io;
window.boson.ids = ids;
window.boson.rpc = rpc;
window.boson.components = {
    instances: new BosonWebComponentsSet(),
};
