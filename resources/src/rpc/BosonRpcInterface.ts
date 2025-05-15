
/**
 * RPC parameters list definition
 */
export type BosonRpcParameters = Array<any> | {
    [parameter: string]: any
};

export default interface BosonRpcInterface {
    /**
     * Executes an external method.
     */
    call(method: string, params: BosonRpcParameters): Promise<any>;
}
