<?php
/**
 * @var non-empty-string $tagName
 * @var non-empty-string $className
 * @var class-string $component
 * @var bool $hasObservedAttributes
 * @var bool $isDebug
 */
?>
class <?php echo $className; ?> extends HTMLElement {
    /**
     * Contains the unique identifier of the component instance.
     *
     * @type {string}
     */
    #id;

    /**
     * Contains a reference to the internals of an HTML element.
     *
     * @type {ElementInternals}
     */
    #internals;

<?php if ($hasObservedAttributes) { ?>
    /**
     * Contains a list of attribute subscriptions.
     *
     * @see https://developer.mozilla.org/en-US/docs/Web/API/Web_components/Using_custom_elements#responding_to_attribute_changes
     * @type {string[]}
     */
    static observedAttributes = <?php echo \json_encode($component::getObservedAttributeNames()); ?>;
<?php } ?>

    constructor() {
        super();

        this.#internals = this.attachInternals();
        this.#id = window.boson.ids.generate();

<?php if ($isDebug) { ?>
        console.info(`[boson] <<?php echo $tagName; ?> /> created`);
<?php } ?>

        // Attach element to globals registry
        window.boson.components.instances[this.#id] = this;
        // Sending a notification about the creation of an element
        window.boson.components.created("<?php echo $tagName; ?>", this.#id);
    }

    connectedCallback() {
        // Double attach element to globals registry (after detaching)
        window.boson.components.instances[this.#id] = this;

<?php if ($isDebug) { ?>
        console.info(`[boson] <<?php echo $tagName; ?> /> connected`);
<?php } ?>

        // Send a notification about the element connection
        window.boson.components.connected(this.#id)
            .then((value) => {
                if (value === null) {
                    return;
                }

                this.attachShadow({mode: 'open'}).innerHTML = value;
            });
    }

    disconnectedCallback() {
        // Detach element from globals registry
        delete window.boson.components.instances[this.#id];

<?php if ($isDebug) { ?>
        console.info(`[boson] <<?php echo $tagName; ?> /> disconnected`);
<?php } ?>

        // Send a notification about the element disconnection
        window.boson.components.disconnected(this.#id);
    }

    attributeChangedCallback(name, oldValue, newValue) {
<?php if ($isDebug) { ?>
        console.info(`[boson] <<?php echo $tagName; ?> ${name}="${newValue}" /> attribute changed`);
<?php } ?>

        // Send a notification about the element attribute change
        window.boson.components.attributeChanged(this.#id, name, newValue, oldValue);
    }
}

customElements.define("<?php echo $tagName; ?>", <?php echo $className; ?>);
