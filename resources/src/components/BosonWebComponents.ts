import type {Optional} from "../common/Optional";
import type BosonWebComponentsSet from "./BosonWebComponentsSet";

type Identifier = string;
type ArgumentsList = string[] | {[key: string]: any};
type AttributeValue = Optional<string>;
type PropertyValue = any;

export type BosonWebComponentsLifecycleMethods = {
    /**
     * Should be called if the component is successfully created.
     *
     * Returns optional content if it should be explicitly set in the
     * component's HTML.
     *
     * @param {string} id A new unique identifier of the created component
     * @param {string} tag The name of the tag to which the specified component
     *        is attached
     */
    created?: (id: string, tag: string) => Promise<Optional<string>>,
    /**
     * Should be called if the component has been mounted to the DOM object.
     *
     * Returns optional contents for Shadow DOM.
     *
     * @param {string} id The ID of the created element
     */
    connected?: (id: string) => Promise<Optional<string>>,
    /**
     * Should be called if the component has been unmounted from the DOM object.
     *
     * @param {string} id The ID of the created element
     */
    disconnected?: (id: string) => void,
    /**
     * Should be called if a certain element instance property has been changed.
     *
     * @param {string} id The ID of the created element
     * @param {string} property The name of the property
     * @param {any} value New value of the property
     */
    propertyChanged?: (id: string, property: Identifier, value: PropertyValue) => void,
    /**
     * Should be called if a certain method is called on an element.
     *
     * Returns the result of executing the method.
     *
     * @param {string} id The ID of the created element
     * @param {string} method The name of the called method
     * @param {string[]|Object} args List of method arguments
     */
    invoke?: (id: string, method: Identifier, args: ArgumentsList) => Promise<any>,
    /**
     * Should be called if a certain event is called on an element.
     *
     * @param {string} id The ID of the created element
     * @param {string} event The name of the fired event
     * @param {string[]|Object} args List of event arguments
     */
    fire?: (id: string, event: Identifier, args: ArgumentsList) => void,
    /**
     * Should be called if a certain element attribute has been changed.
     *
     * @param {string} id The ID of the created element
     * @param {string} attribute The name of the attribute
     * @param {string|undefined} value New value of the attribute
     * @param {string|undefined} previous Previous attribute value
     */
    attributeChanged?: (id: string, attribute: Identifier, value: AttributeValue, previous: AttributeValue) => void,
};

export type BosonWebComponentsRuntime = {
    instances: BosonWebComponentsSet,
};

export type BosonWebComponents
    = BosonWebComponentsLifecycleMethods
    & BosonWebComponentsRuntime;
