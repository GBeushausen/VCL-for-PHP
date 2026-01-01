<?php

declare(strict_types=1);

namespace VCL\Core;

/**
 * Component is the base class for all VCL components.
 *
 * Components are objects that can be manipulated at design time and have
 * owner/parent relationships. They support:
 * - Ownership hierarchy (owner manages lifecycle)
 * - Session persistence (properties serialized to session)
 * - Event handling
 * - Named component lookup
 */
class Component extends VCLObject
{
    // Backing fields
    private string $_name = '';
    private ?Component $_owner = null;
    private int $_tag = 0;

    /** @var array<Component> Child components */
    protected array $_components = [];

    /**
     * Component name (must be unique within owner)
     */
    public string $Name {
        get => $this->_name;
        set {
            // Validate name format
            if ($value !== '' && !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $value)) {
                throw new \InvalidArgumentException("Invalid component name: {$value}");
            }

            // Check for duplicates in owner
            if ($this->_owner !== null && $value !== '') {
                foreach ($this->_owner->_components as $sibling) {
                    if ($sibling !== $this && $sibling->_name === $value) {
                        throw new \RuntimeException("Duplicate component name: {$value}");
                    }
                }
            }

            // Update parent's childnames index
            if ($this->_owner !== null) {
                if ($this->_name !== '') {
                    unset($this->_owner->_childnames[$this->_name]);
                }
                if ($value !== '') {
                    $this->_owner->_childnames[$value] = $this;
                }
            }

            $this->_name = $value;
        }
    }

    /**
     * Owner component (manages this component's lifecycle)
     */
    public ?Component $Owner {
        get => $this->_owner;
    }

    /**
     * User-defined tag value for custom data
     */
    public int $Tag {
        get => $this->_tag;
        set => $this->_tag = $value;
    }

    /**
     * Number of child components
     */
    public int $ComponentCount {
        get => count($this->_components);
    }

    public function __construct(?Component $owner = null)
    {
        parent::__construct();

        if ($owner !== null) {
            $this->setOwner($owner);
        }
    }

    /**
     * Set the owner of this component
     */
    protected function setOwner(?Component $owner): void
    {
        if ($this->_owner === $owner) {
            return;
        }

        // Remove from old owner
        if ($this->_owner !== null) {
            $this->_owner->removeComponent($this);
        }

        $this->_owner = $owner;

        // Add to new owner
        if ($owner !== null) {
            $owner->insertComponent($this);
        }
    }

    /**
     * Add a component as child
     */
    public function insertComponent(Component $component): void
    {
        if (!in_array($component, $this->_components, true)) {
            $this->_components[] = $component;

            if ($component->_name !== '') {
                $this->_childnames[$component->_name] = $component;
            }
        }
    }

    /**
     * Remove a child component
     */
    public function removeComponent(Component $component): void
    {
        $key = array_search($component, $this->_components, true);
        if ($key !== false) {
            unset($this->_components[$key]);
            $this->_components = array_values($this->_components);

            if ($component->_name !== '') {
                unset($this->_childnames[$component->_name]);
            }
        }
    }

    /**
     * Get child component by index
     */
    public function getComponent(int $index): ?Component
    {
        return $this->_components[$index] ?? null;
    }

    /**
     * Find component by name
     */
    public function findComponent(string $name): ?Component
    {
        return $this->_childnames[$name] ?? null;
    }

    /**
     * Iterate over all child components
     *
     * @return \Generator<Component>
     */
    public function getComponents(): \Generator
    {
        foreach ($this->_components as $component) {
            yield $component;
        }
    }

    /**
     * Destroy this component and all children
     */
    public function destroy(): void
    {
        // Destroy children first
        foreach ($this->_components as $child) {
            $child->destroy();
        }
        $this->_components = [];
        $this->_childnames = [];

        // Remove from owner
        if ($this->_owner !== null) {
            $this->_owner->removeComponent($this);
            $this->_owner = null;
        }
    }
}
