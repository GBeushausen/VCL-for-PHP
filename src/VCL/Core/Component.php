<?php

declare(strict_types=1);

namespace VCL\Core;

use VCL\Core\Exception\DuplicateNameException;

/**
 * Component is the common ancestor of all component classes.
 *
 * A base class for components that provides owner relationship properties and
 * basic methods for calling events.
 *
 * Components are persistent objects that have the following capabilities:
 * - IDE integration
 * - Ownership (A owns B means A is responsible for destroying B)
 * - Streaming and filing
 *
 * PHP 8.4 version with Property Hooks for clean syntax while maintaining
 * backwards compatibility with legacy getters/setters.
 */
class Component extends Persistent
{
    // Backing fields (public for legacy compatibility)
    public string $_name = '';
    public ?Component $owner = null;
    public int $_tag = 0;
    public int $_controlstate = 0;
    public string $lastresourceread = '';
    public array $reallastresourceread = [];
    public bool $alreadycreated = false;
    protected string $_namepath = '';

    /** @var Collection Child components */
    public ?Collection $components = null;

    /**
     * Component name (must be unique within owner)
     */
    public string $Name {
        get => $this->_name;
        set {
            global $checkduplicatenames;

            if ($checkduplicatenames ?? true) {
                if ($value !== $this->_name) {
                    if ($this->owner !== null && !$this->owner->classNameIs('application')) {
                        if ($value !== '' && isset($this->owner->_childnames[$value])) {
                            throw new DuplicateNameException($value);
                        }
                        if ($this->_name !== '') {
                            unset($this->owner->_childnames[$this->_name]);
                        }
                    }
                }
            }

            $this->_name = $value;

            if ($this->owner !== null && $value !== '') {
                $this->owner->_childnames[$value] = $this;
            }
        }
    }

    /**
     * Owner component (manages this component's lifecycle)
     */
    public ?Component $Owner {
        get => $this->owner;
    }

    /**
     * User-defined tag value for custom data
     */
    public int $Tag {
        get => $this->_tag;
        set => $this->_tag = $value;
    }

    /**
     * Control state flags (csLoading, csDesigning)
     */
    public int $ControlState {
        get => $this->_controlstate;
        set => $this->_controlstate = $value;
    }

    /**
     * Number of child components
     */
    public int $ComponentCount {
        get => $this->components?->count() ?? 0;
    }

    public function __construct(?Component $owner = null)
    {
        parent::__construct();

        $this->components = new Collection();
        $this->owner = null;
        $this->_name = '';
        $this->_controlstate = 0;

        if ($owner !== null) {
            if (!is_object($owner)) {
                throw new \InvalidArgumentException('Owner must be an object');
            }
            $this->owner = $owner;
            $this->owner->insertComponent($this);
        }
    }

    // =========================================================================
    // LIFECYCLE METHODS
    // =========================================================================

    /**
     * Called after the form file has been read into memory.
     * Override this to initialize data that depends on other components.
     */
    public function loaded(): void
    {
        // Override in subclasses
    }

    /**
     * Calls loaded() on all children recursively.
     */
    public function loadedChildren(): void
    {
        foreach ($this->components->items as $v) {
            $v->loaded();
        }
    }

    /**
     * Called before init(). Override for pre-initialization logic.
     */
    public function preinit(): void
    {
        foreach ($this->components->items as $v) {
            $v->preinit();
        }
    }

    /**
     * Initialize the component.
     * Override this to fire events after all components are loaded.
     */
    public function init(): void
    {
        $comps = $this->components->items;
        foreach ($comps as $v) {
            $v->init();
        }
    }

    // =========================================================================
    // COMPONENT MANAGEMENT
    // =========================================================================

    /**
     * Inserts a component into the component's collection.
     */
    public function insertComponent(Component $component): void
    {
        $component->owner = $this;
        $this->_childnames[$component->_name] = $component;
        $this->components->add($component);
    }

    /**
     * Removes a component from the component's collection.
     */
    public function removeComponent(Component $component): void
    {
        $this->components->remove($component);
        unset($this->_childnames[$component->_name]);
    }

    /**
     * Get child component by index.
     */
    public function getComponent(int $index): ?Component
    {
        return $this->components->get($index);
    }

    /**
     * Find component by name.
     */
    public function findComponent(string $name): ?Component
    {
        return $this->_childnames[$name] ?? null;
    }

    /**
     * Read the components collection.
     */
    public function readComponents(): Collection
    {
        return $this->components;
    }

    /**
     * Read component count.
     */
    public function readComponentCount(): int
    {
        return $this->components->count();
    }

    // =========================================================================
    // STATE & PATH
    // =========================================================================

    /**
     * Read control state.
     */
    public function readControlState(): int
    {
        return $this->_controlstate;
    }

    /**
     * Write control state.
     */
    public function writeControlState(int $value): void
    {
        $this->_controlstate = $value;
    }

    /**
     * Get the unique path for this component.
     */
    public function readNamePath(): string
    {
        if ($this->_name !== '') {
            $result = $this->_name;
        } else {
            $result = $this->className();
        }

        $owner = $this->readOwner();
        if ($owner !== null) {
            $s = $owner->readNamePath();
            if ($s !== '') {
                $result = $s . '.' . $result;
            }
        }

        return $result;
    }

    /**
     * Get the owner of this component.
     */
    public function readOwner(): ?Component
    {
        return $this->owner;
    }

    // =========================================================================
    // LEGACY GETTER/SETTER METHODS
    // =========================================================================

    public function getName(): string
    {
        return $this->_name;
    }

    public function setName(string $value): void
    {
        $this->Name = $value;
    }

    public function defaultName(): string
    {
        return '';
    }

    public function getTag(): int
    {
        return $this->_tag;
    }

    public function setTag(int $value): void
    {
        $this->_tag = $value;
    }

    public function defaultTag(): int
    {
        return 0;
    }

    // =========================================================================
    // SERIALIZATION
    // =========================================================================

    /**
     * Serializes all children to session.
     */
    public function serializeChildren(): void
    {
        foreach ($this->components->items as $v) {
            $v->serialize();
        }
    }

    /**
     * Unserializes all children from session.
     */
    public function unserializeChildren(): void
    {
        foreach ($this->components->items as $v) {
            $v->unserialize();
        }
    }

    // =========================================================================
    // EVENTS
    // =========================================================================

    /**
     * Calls a server event.
     *
     * @param string $event Name of the event to call
     * @param mixed $params Parameters to send to the event handler
     * @return mixed Calling event result
     */
    public function callEvent(string $event, mixed $params): mixed
    {
        // Check ACL if not a Page
        if (!$this->inheritsFrom('Page')) {
            if (function_exists('acl_isallowed') && !acl_isallowed($this->className() . '::' . $this->_name, 'Execute')) {
                return null;
            }
        }

        $ievent = '_' . $event;
        if (property_exists($this, $ievent) && $this->$ievent !== null) {
            $eventHandler = $this->$ievent;
            if ($this->owner !== null && !$this->owner->classNameIs('application')) {
                return $this->owner->$eventHandler($this, $params);
            }
            return $this->$eventHandler($this, $params);
        }

        return null;
    }

    /**
     * Dumps javascript code for an event.
     */
    public function dumpJSEvent(?string $event): void
    {
        if ($event === null || $this->owner === null) {
            return;
        }

        $defName = $this->owner->Name . '_' . $event;
        if (!defined($defName)) {
            define($defName, 1);
            echo "function {$event}(event)\n";
            echo "{\n\n";
            echo "var params=null;\n";

            if ($this->inheritsFrom('CustomPage')) {
                $this->$event($this, []);
            } elseif ($this->owner !== null) {
                $this->owner->$event($this, []);
            }

            echo "\n}\n\n";
        }
    }

    /**
     * Resolves object property references after loading.
     *
     * @param mixed $value String name or object reference
     * @return mixed Resolved object or original value
     */
    public function fixupProperty(mixed $value): mixed
    {
        if (($this->_controlstate & CS_DESIGNING) === CS_DESIGNING) {
            return $value;
        }

        if (empty($value) || is_object($value)) {
            return $value;
        }

        $form = $this->inheritsFrom('CustomPage') ? $this : $this->owner;

        if (str_contains($value, '.')) {
            $pieces = explode('.', $value);
            $count = count($pieces);

            if ($count === 2) {
                $form = $pieces[0];
                $value = $pieces[1];
            } elseif ($count === 3) {
                $form = $pieces[1];
                $value = $pieces[2];
            }

            global $$form;
            $form = $$form ?? $form;
        }

        if (is_object($form) && isset($form->$value) && is_object($form->$value)) {
            return $form->$value;
        }

        return $value;
    }

    // =========================================================================
    // HTMX SUPPORT
    // =========================================================================

    /**
     * Generate htmx attributes for an event.
     *
     * @param string $phpEvent The PHP event handler name
     * @param string $trigger The htmx trigger (e.g., 'click', 'change', 'submit')
     * @param string $target The target element selector (default: this element)
     * @param string $swap The swap method (innerHTML, outerHTML, beforeend, etc.)
     * @return string HTML attributes for htmx
     */
    public function generateHtmxEvent(
        string $phpEvent,
        string $trigger = 'click',
        string $target = '',
        string $swap = 'innerHTML'
    ): string {
        global $scriptfilename;
        $ownerName = $this->owner !== null ? $this->owner->Name : '';
        $action = $scriptfilename ?? $_SERVER['PHP_SELF'] ?? '';

        $attrs = [];
        $attrs[] = sprintf('hx-post="%s"', htmlspecialchars($action));
        $attrs[] = sprintf('hx-trigger="%s"', htmlspecialchars($trigger));

        if ($target !== '') {
            $attrs[] = sprintf('hx-target="%s"', htmlspecialchars($target));
        }

        $attrs[] = sprintf('hx-swap="%s"', htmlspecialchars($swap));

        // Include form values
        $attrs[] = sprintf('hx-include="[name^=\'%s\']"', htmlspecialchars($ownerName));

        // Add VCL metadata (use JSON_HEX flags for safe HTML attribute escaping)
        $attrs[] = sprintf('hx-vals=\'%s\'', json_encode([
            '_vcl_form' => $ownerName,
            '_vcl_control' => $this->_name,
            '_vcl_event' => $phpEvent,
        ], JSON_HEX_APOS | JSON_HEX_QUOT));

        return ' ' . implode(' ', $attrs) . ' ';
    }

    /**
     * Generate htmx attributes for a form submission.
     *
     * @param string $phpEvent The PHP event handler name
     * @param string $target The target element selector
     * @param string $swap The swap method
     * @return string HTML attributes for htmx
     */
    public function generateHtmxSubmit(
        string $phpEvent,
        string $target = '',
        string $swap = 'innerHTML'
    ): string {
        return $this->generateHtmxEvent($phpEvent, 'submit', $target, $swap);
    }

    /**
     * Generate JavaScript code for programmatic htmx request.
     *
     * @param string $phpEvent The PHP event handler name
     * @param string $target Optional target selector
     * @return string JavaScript code
     */
    public function htmxCall(string $phpEvent, string $target = ''): string
    {
        global $scriptfilename;
        $ownerName = $this->owner !== null ? $this->owner->Name : '';
        $action = $scriptfilename ?? $_SERVER['PHP_SELF'] ?? '';

        $targetJs = $target !== '' ? json_encode($target) : 'null';

        return sprintf(
            "htmx.ajax('POST', %s, {values: {_vcl_form: %s, _vcl_control: %s, _vcl_event: %s}, target: %s});\n",
            json_encode($action),
            json_encode($ownerName),
            json_encode($this->_name),
            json_encode($phpEvent),
            $targetJs
        );
    }

    /**
     * Generate hidden input fields for htmx VCL metadata.
     *
     * @param string $phpEvent The PHP event handler name
     * @return string HTML hidden inputs
     */
    public function getHtmxHiddenFields(string $phpEvent): string
    {
        $ownerName = $this->owner !== null ? $this->owner->Name : '';

        return sprintf(
            '<input type="hidden" name="_vcl_form" value="%s">' .
            '<input type="hidden" name="_vcl_control" value="%s">' .
            '<input type="hidden" name="_vcl_event" value="%s">',
            htmlspecialchars($ownerName),
            htmlspecialchars($this->_name),
            htmlspecialchars($phpEvent)
        );
    }

    // =========================================================================
    // CODE GENERATION
    // =========================================================================

    /**
     * Override to dump component-specific javascript.
     */
    public function dumpJavascript(): void
    {
        // Override in subclasses
    }

    /**
     * Override to dump component-specific header code.
     */
    public function dumpHeaderCode(): void
    {
        // Override in subclasses
    }

    /**
     * Override to dump form items (hidden fields etc.).
     */
    public function dumpFormItems(): void
    {
        // Override in subclasses
    }

    /**
     * Dumps javascript for all children.
     */
    public function dumpChildrenJavascript(): void
    {
        $this->dumpJavascript();
        foreach ($this->components->items as $v) {
            if ($v->inheritsFrom('Control')) {
                if (method_exists($v, 'canShow') && $v->canShow()) {
                    $v->dumpJavascript();
                }
            } else {
                $v->dumpJavascript();
            }
        }
    }

    /**
     * Dumps header code for all children.
     */
    public function dumpChildrenHeaderCode(bool $returnContents = false): string
    {
        if ($returnContents) {
            ob_start();
        }

        foreach ($this->components->items as $v) {
            if ($v->inheritsFrom('Control')) {
                if (method_exists($v, 'canShow') && $v->canShow()) {
                    $v->dumpHeaderCode();
                }
            } else {
                $v->dumpHeaderCode();
            }
        }

        if ($returnContents) {
            $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        }

        return '';
    }

    /**
     * Dumps form items for all children.
     */
    public function dumpChildrenFormItems(bool $returnContents = false): string
    {
        if ($returnContents) {
            ob_start();
        }

        foreach ($this->components->items as $v) {
            if ($v->inheritsFrom('Control')) {
                if (method_exists($v, 'canShow') && $v->canShow()) {
                    $v->dumpFormItems();
                }
            } else {
                $v->dumpFormItems();
            }
        }

        if ($returnContents) {
            $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        }

        return '';
    }

    // =========================================================================
    // RPC & DATA-AWARE
    // =========================================================================

    /**
     * Read accessibility for RPC.
     */
    public function readAccessibility(string $method, int $defAccessibility): int
    {
        if (function_exists('use_unit')) {
            use_unit('rpc/rpc.inc.php');
        }
        return defined('Accessibility_Fail') ? Accessibility_Fail : 0;
    }

    /**
     * Destroy this component and all children.
     */
    public function destroy(): void
    {
        foreach ($this->components->items as $child) {
            $child->destroy();
        }
        $this->components->clear();
        $this->_childnames = [];

        if ($this->owner !== null) {
            $this->owner->removeComponent($this);
            $this->owner = null;
        }
    }

    // =========================================================================
    // RESOURCE LOADING (Stub - full implementation in Streaming)
    // =========================================================================

    /**
     * Loads this component from a resource file.
     */
    public function loadResource(string $filename, bool $inherited = false, bool $storeLastResource = true): void
    {
        // Implementation moved to Streaming namespace
        // This stub maintains API compatibility
        if ($storeLastResource) {
            $this->lastresourceread = $filename;
        }
    }

    /**
     * Reads a component from a resource file.
     */
    public function readFromResource(string $filename = '', bool $createObjects = true): void
    {
        // Implementation requires Streaming classes
        // See VCL\Core\Streaming\Reader
    }
}
