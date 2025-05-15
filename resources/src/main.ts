
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

/**
 * Prepare public accessor instance.
 */
window.boson = window.boson || {};

try {
    window.boson.io = TransportFactory.createFromGlobals();
} catch (e) {
    console.error('Failed to initialize IPC subsystem', e);
}

try {
    window.boson.ids = IdGeneratorFactory.createFromGlobals();
} catch (e) {
    console.error('Failed to initialize ID generator subsystem', e);
}

try {
    if (!window.boson.io || !window.boson.ids) {
        throw new Error('Could not initialize RPC: Requires IPC and ID generator subsystems');
    }

    window.boson.rpc = new BosonRpc(window.boson.io, window.boson.ids);
} catch (e) {
    console.error('Failed to initialize RPC subsystem', e);
}

window.boson.components = window.boson.components || {};

try {
    window.boson.components.instances = new BosonWebComponentsSet();
} catch (e) {
    console.error('Failed to initialize Web Components subsystem', e);
}
