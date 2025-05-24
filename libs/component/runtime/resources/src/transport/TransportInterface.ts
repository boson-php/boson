
export interface TransportInterface {
    /**
     * Send message string to the PHP runtime.
     *
     * @param {string} message
     */
    send(message: string): void;
}
