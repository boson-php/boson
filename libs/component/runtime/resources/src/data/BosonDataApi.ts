import type {BosonDataResponder} from "./BosonDataResponder";
import type {BosonDataRejector} from "./BosonDataRejector";

import type {IdType} from "../id-generator/IdGeneratorInterface";

export type BosonDataApi<T extends IdType> = {
    respond: BosonDataResponder<T>,
    reject: BosonDataRejector<T>,
}
