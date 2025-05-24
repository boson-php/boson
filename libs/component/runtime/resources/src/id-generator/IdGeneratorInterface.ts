
export type IdType = string|number;

export default interface IdGeneratorInterface<T extends IdType> {
    /**
     * Gets random hexadecimal string of expected
     * length (2 chars per byte).
     *
     * @param {number} length
     */
    generate(length?: number): T;
}
