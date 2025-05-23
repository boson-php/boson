<?php
/**
 * @var non-empty-string $tagName
 * @var non-empty-string $className
 * @var class-string $component
 * @var bool $hasShadowRoot
 * @var list<non-empty-string> $attributeNames
 * @var list<non-empty-string> $propertyNames
 * @var list<non-empty-string> $methodNames
 * @var array<non-empty-string, non-empty-string> $eventListeners
 * @var bool $isDebug
 */
?>
class <?=$className?> extends HTMLElement {
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

<?php if ($isDebug): ?>
<?php   if (\PHP_OS_FAMILY === 'Darwin'): ?>
    /** Apple WebKit does not support ASCI escape sequences */
    #debugPrefix = '[boson(debug:true)] ';
<?php   else: ?>
    #debugPrefix = '\x1B[37;3m[boson(debug:true)]\x1B[m ';
<?php   endif ?>
<?php endif ?>

<?php if ($attributeNames !== []): ?>
    /**
     * Contains a list of attribute subscriptions.
     *
     * @see https://developer.mozilla.org/en-US/docs/Web/API/Web_components/Using_custom_elements#responding_to_attribute_changes
     * @return {string[]}
     */
    static get observedAttributes() {
        return <?=\json_encode($attributeNames)?>;
    }
<?php endif ?>

<?php foreach ($propertyNames as $propertyName): ?>
    #<?=$propertyName?> = null;

    get <?=$propertyName?>() {
        return this.#<?=$propertyName?>;
    }

    set <?=$propertyName?>(value) {
        this.#<?=$propertyName?> = value;
        window.boson.components.propertyChanged(this.#id, "<?=$propertyName?>", JSON.stringify(value));
    }
<?php endforeach ?>

    constructor() {
        super();

        this.#internals = this.attachInternals();
        this.#id = window.boson.ids.generate();

<?php if ($isDebug): ?>
        // You may set ApplicationCreateInfo::$debug to false to diable this logs
        console.log(`${this.#debugPrefix}<<?=$tagName?> /> created`);
<?php endif ?>

<?php if ($hasShadowRoot): ?>
        this.attachShadow({mode: 'open'});
<?php endif ?>

        // Attach element to globals registry
        window.boson.components.instances.attach(this.#id, this);

        // Register event listeners
<?php foreach ($eventListeners as $eventName => $eventArgs): ?>
        this.addEventListener("<?=$eventName?>", (e) =>
            window.boson.components.fire(this.#id, "<?=$eventName?>", <?=$eventArgs?>)
        );
<?php endforeach ?>

        // Sending a notification about the creation of an element
        window.boson.components.created("<?=$tagName?>", this.#id)
            .then((value) => {
                if (value === null) {
                    return;
                }

<?php if ($isDebug): ?>
                // You may set ApplicationCreateInfo::$debug to false to diable this logs
                console.log(`${this.#debugPrefix}<<?=$tagName?> /> render raw ${value}`);
<?php endif ?>

                this.innerHTML = value;
            });

        return this;
    }

<?php foreach ($methodNames as $methodName): ?>

    async <?=$methodName?>() {
        return await window.boson.components.invoke(this.#id, "<?=$methodName?>", Array.prototype.slice.call(arguments));
    }

<?php endforeach ?>

    connectedCallback() {
        // Double attach element to globals registry (after detaching)
        window.boson.components.instances.attach(this.#id, this);

<?php if ($isDebug): ?>
        // You may set ApplicationCreateInfo::$debug to false to diable this logs
        console.log(`${this.#debugPrefix}<<?=$tagName?> /> connected`);
<?php endif ?>

        // Send a notification about the element connection
        window.boson.components.connected(this.#id)
            .then((value) => {
                if (value === null) {
                    return;
                }

<?php if ($isDebug): ?>
                // You may set ApplicationCreateInfo::$debug to false to diable this logs
                console.log(`${this.#debugPrefix}<<?=$tagName?> /> render shadow ${value}`);
<?php endif ?>

                this.shadowRoot.innerHTML = value;
            });
    }

    disconnectedCallback() {
        // Detach element from globals registry
        window.boson.components.instances.detach(this.#id);

<?php if ($isDebug): ?>
        // You may set ApplicationCreateInfo::$debug to false to diable this logs
        console.log(`${this.#debugPrefix}<<?=$tagName?> /> disconnected`);
<?php endif ?>

        // Send a notification about the element disconnection
        window.boson.components.disconnected(this.#id);
    }

    attributeChangedCallback(name, oldValue, newValue) {
<?php if ($isDebug): ?>
        // You may set ApplicationCreateInfo::$debug to false to diable this logs
        console.log(`${this.#debugPrefix}<<?=$tagName?> ${name}="${newValue}" /> attribute changed`);
<?php endif ?>

        // Send a notification about the element attribute change
        window.boson.components.attributeChanged(this.#id, name, newValue, oldValue);
    }
}

customElements.define("<?=$tagName?>", <?=$className?>);
