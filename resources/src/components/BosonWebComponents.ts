import type {Optional} from "../common/Optional";
import type BosonWebComponentsSet from "./BosonWebComponentsSet";

type AttributeValue = Optional<string>;

export type BosonWebComponentsLifecycleMethods = {
    created?: (id: string, tag: string) => Promise<Optional<string>>,
    connected?: (id: string) => Promise<Optional<string>>,
    disconnected?: (id: string) => void,
    invoke?: (id: string, method: string, args: any) => Promise<any>,
    attributeChanged?: (id: string, attribute: string, value: AttributeValue, previous: AttributeValue) => void,
};

export type BosonWebComponentsRuntime = {
    instances: BosonWebComponentsSet,
};

export type BosonWebComponents
    = BosonWebComponentsLifecycleMethods
    & BosonWebComponentsRuntime;
