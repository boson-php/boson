import type {IdType} from "../id-generator/IdGeneratorInterface";

export type BosonDataResponder<T extends IdType> = (id: T, result: any) => void;
