import type {IdType} from "../id-generator/IdGeneratorInterface";

export type BosonDataRejector<T extends IdType> = (id: T, error: Error) => void;
