<?php

declare(strict_types=1);

namespace VCL\Core;

use VCL\Core\Exception\PropertyNotFoundException;

/**
 * VCLObject is the ultimate ancestor of all VCL objects and components.
 *
 * This class encapsulates fundamental behavior common to all objects:
 * - Instance creation and destruction
 * - Runtime type information (RTTI) about class and properties
 * - Property virtualization through PHP 8.4 property hooks or magic methods
 *
 * Use VCLObject as a base class for simple objects that do not need
 * session persistence. For persistent objects, use Persistent instead.
 */
class VCLObject
{
    /**
     * Global input object for request handling
     */
    protected ?Input $input = null;

    /**
     * Child components indexed by name (used by Component descendants)
     * @var array<string, object>
     */
    public $_childnames = [];

    public function __construct()
    {
        global $input;
        $this->input = $input instanceof Input ? $input : null;
    }

    /**
     * Returns the class name of this instance
     */
    public function className(): string
    {
        return static::class;
    }

    /**
     * Returns the short class name without namespace
     */
    public function shortClassName(): string
    {
        $parts = explode('\\', static::class);
        return end($parts);
    }

    /**
     * Determines whether this object matches a specific class name
     */
    public function classNameIs(string $name): bool
    {
        return strcasecmp($this->shortClassName(), $name) === 0
            || strcasecmp(static::class, $name) === 0;
    }

    /**
     * Check if a method exists on this object instance
     */
    public function methodExists(string $method): bool
    {
        return method_exists($this, $method);
    }

    /**
     * Returns the parent class name
     */
    public function classParent(): string|false
    {
        return get_parent_class($this);
    }

    /**
     * Determines if this object inherits from a specific class
     */
    public function inheritsFrom(string $class): bool
    {
        return is_a($this, $class, false)
            || $this->classNameIs($class);
    }

    /**
     * Reads a property value from POST/GET streams
     */
    public function readProperty(
        string $propertyName,
        string $valueName,
        InputSource $source = InputSource::POST
    ): void {
        $array = $source->getArray();
        if (isset($array[$valueName])) {
            $this->$propertyName = $array[$valueName];
        }
    }

    /**
     * Magic getter for virtual properties
     *
     * Searches for methods in this order:
     * 1. get{PropertyName}()
     * 2. read{PropertyName}()
     * 3. Child component with matching name (for Component descendants)
     *
     * @throws PropertyNotFoundException if property not found
     */
    public function __get(string $name): mixed
    {
        // Try get{Name}() method
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        // Try read{Name}() method
        $reader = 'read' . $name;
        if (method_exists($this, $reader)) {
            return $this->$reader();
        }

        // Try child component lookup
        if (isset($this->_childnames[$name])) {
            return $this->_childnames[$name];
        }

        throw new PropertyNotFoundException($this->shortClassName(), $name);
    }

    /**
     * Magic setter for virtual properties
     *
     * Searches for methods in this order:
     * 1. set{PropertyName}()
     * 2. write{PropertyName}()
     *
     * @throws PropertyNotFoundException if property not found
     */
    public function __set(string $name, mixed $value): void
    {
        // Try set{Name}() method
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
            return;
        }

        // Try write{Name}() method
        $writer = 'write' . $name;
        if (method_exists($this, $writer)) {
            $this->$writer($value);
            return;
        }

        throw new PropertyNotFoundException($this->shortClassName(), $name);
    }

    /**
     * Check if a virtual property is set
     */
    public function __isset(string $name): bool
    {
        return method_exists($this, 'get' . $name)
            || method_exists($this, 'read' . $name)
            || isset($this->_childnames[$name]);
    }
}

// Legacy alias is registered in LegacyAliases.php
