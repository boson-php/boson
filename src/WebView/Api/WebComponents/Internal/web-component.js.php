<?php
/**
 * @var non-empty-string $tagName
 * @var non-empty-string $className
 * @var class-string $component
 * @var bool $hasObservedAttributes
 * @var bool $hasShadowRoot
 * @var list<non-empty-string> $methodNames
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

<?php if ($isDebug) { ?>
<?php   if (\PHP_OS_FAMILY === 'Darwin') { ?>
    #debugPrefix = '[boson(debug:true)] ';
<?php   } else { ?>
    #debugPrefix = '\x1B[37;3m[boson(debug:true)]\x1B[m ';
<?php   } ?>
<?php } ?>

<?php if ($hasObservedAttributes) { ?>
    /**
     * Contains a list of attribute subscriptions.
     *
     * @see https://developer.mozilla.org/en-US/docs/Web/API/Web_components/Using_custom_elements#responding_to_attribute_changes
     * @return {string[]}
     */
    static get observedAttributes() {
        return <?php echo \json_encode($component::getObservedAttributeNames()); ?>;
    }
<?php } ?>

    constructor() {
        super();

        this.#internals = this.attachInternals();
        this.#id = window.boson.ids.generate();

<?php if ($isDebug) { ?>
        // You may set ApplicationCreateInfo::$debug to false to diable this logs
        console.log(`${this.#debugPrefix}<<?php echo $tagName; ?> /> created`);
<?php } ?>

<?php if ($hasShadowRoot) { ?>
        this.attachShadow({mode: 'open'});
<?php } ?>

        // Attach element to globals registry
        window.boson.components.instances.attach(this.#id, this);
        // Sending a notification about the creation of an element
        window.boson.components.created("<?php echo $tagName; ?>", this.#id)
            .then((value) => {
                if (value === null) {
                    return;
                }

<?php if ($isDebug) { ?>
                // You may set ApplicationCreateInfo::$debug to false to diable this logs
                console.log(`${this.#debugPrefix}<<?php echo $tagName; ?> /> render raw ${value}`);
<?php } ?>

                this.innerHTML = value;
            });

        return this;
    }

<?php foreach ($methodNames as $methodName) { ?>

    <?php echo $methodName; ?>() {
        return window.boson.components.invoke(this.#id, "<?php echo $methodName; ?>", Array.prototype.slice.call(arguments));
    }

<?php } ?>

    connectedCallback() {
        // Double attach element to globals registry (after detaching)
        window.boson.components.instances.attach(this.#id, this);

<?php if ($isDebug) { ?>
        // You may set ApplicationCreateInfo::$debug to false to diable this logs
        console.log(`${this.#debugPrefix}<<?php echo $tagName; ?> /> connected`);
<?php } ?>

        // Send a notification about the element connection
        window.boson.components.connected(this.#id)
            .then((value) => {
                if (value === null) {
                    return;
                }

<?php if ($isDebug) { ?>
                // You may set ApplicationCreateInfo::$debug to false to diable this logs
                console.log(`${this.#debugPrefix}<<?php echo $tagName; ?> /> render shadow ${value}`);
<?php } ?>

                this.shadowRoot.innerHTML = value;
            });
    }

    disconnectedCallback() {
        // Detach element from globals registry
        window.boson.components.instances.detach(this.#id);

<?php if ($isDebug) { ?>
        // You may set ApplicationCreateInfo::$debug to false to diable this logs
        console.log(`${this.#debugPrefix}<<?php echo $tagName; ?> /> disconnected`);
<?php } ?>

        // Send a notification about the element disconnection
        window.boson.components.disconnected(this.#id);
    }

    attributeChangedCallback(name, oldValue, newValue) {
<?php if ($isDebug) { ?>
        // You may set ApplicationCreateInfo::$debug to false to diable this logs
        console.log(`${this.#debugPrefix}<<?php echo $tagName; ?> ${name}="${newValue}" /> attribute changed`);
<?php } ?>

        // Send a notification about the element attribute change
        window.boson.components.attributeChanged(this.#id, name, newValue, oldValue);
    }
}

customElements.define("<?php echo $tagName; ?>", <?php echo $className; ?>);
