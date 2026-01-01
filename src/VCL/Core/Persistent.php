<?php

declare(strict_types=1);

namespace VCL\Core;

use VCL\Core\Exception\AssignException;

/**
 * A base class for persistent objects which can be serialized/unserialized.
 *
 * If you want to create a component that has persistence capabilities, inherit
 * from this class. The internal session handling uses properties and methods
 * found on this class to serialize/unserialize components to the session
 * and recover application state.
 */
class Persistent extends VCLObject
{
    /**
     * Returns the full path to identify this component.
     * Used for session serialization key generation.
     */
    public function readNamePath(): string
    {
        $result = $this->className();
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
     * Owner of the component.
     * In Persistent, always returns null. Overridden in Component.
     */
    public function readOwner(): mixed
    {
        return null;
    }

    /**
     * Assigns the source properties to this object.
     *
     * @param Persistent|null $source Object to get assigned to this object
     */
    public function assign(?Persistent $source): void
    {
        if ($source !== null) {
            $source->assignTo($this);
        } else {
            $this->assignError(null);
        }
    }

    /**
     * Assigns this object to another object.
     * Override this method to implement the copy logic.
     *
     * @param Persistent $dest Object to assign this object to
     */
    public function assignTo(Persistent $dest): void
    {
        $dest->assignError($this);
    }

    /**
     * Raises an assignment error.
     *
     * @param Persistent|null $source Component tried to assign
     * @throws AssignException
     */
    public function assignError(?Persistent $source): never
    {
        $sourceName = $source !== null ? $source->className() : 'null';
        throw new AssignException($sourceName, $this->className());
    }

    /**
     * Stores this object into the session.
     *
     * Uses PHP reflection to get published properties (those with setters)
     * and store them in the session. Only properties that differ from defaults
     * are stored.
     */
    public function serialize(): void
    {
        $owner = $this->readOwner();

        if ($owner === null) {
            global $exceptions_enabled;
            if ($exceptions_enabled ?? true) {
                throw new \RuntimeException('Cannot serialize a component without an owner');
            }
            return;
        }

        $namePath = $this->readNamePath();
        $_SESSION['insession.' . $namePath] = 1;

        $refClass = new \ReflectionClass($this->className());
        $methods = $refClass->getMethods();

        foreach ($methods as $method) {
            $methodName = $method->name;

            // Check for setter methods (set*)
            if (!str_starts_with($methodName, 'set')) {
                continue;
            }

            $propName = substr($methodName, 3);

            // Get property value
            if ($propName === 'Name') {
                $propValue = $this->_name ?? '';
            } else {
                $propValue = $this->$propName ?? null;
            }

            // Handle object properties
            if (is_object($propValue)) {
                if ($propValue instanceof Component) {
                    $aOwner = $propValue->readOwner();
                    $apropValue = $aOwner !== null ? $aOwner->getName() . '.' : '';
                    $propValue = $apropValue . $propValue->getName();
                } elseif ($propValue instanceof Persistent) {
                    $propValue->serialize();
                    continue;
                }
            }

            // Only serialize non-object values that are allowed
            if (!is_object($propValue) && $this->allowSerialize($propName)) {
                // Check if value differs from default
                $defMethod = 'default' . $propName;
                if (method_exists($this, $defMethod)) {
                    $defValue = $this->$defMethod();
                    if ($this->typeSafeEqual($defValue, $propValue)) {
                        unset($_SESSION[$namePath . '.' . $propName]);
                        continue;
                    }
                }

                $_SESSION[$namePath . '.' . $propName] = $propValue;
            }
        }

        // Serialize children if this is a Component
        if ($this instanceof Component) {
            $this->serializeChildren();
        }
    }

    /**
     * Allows filtering which properties can be serialized.
     * Override to prevent specific properties from being serialized.
     *
     * @param string $propName Name of the property
     * @return bool True if the property can be serialized
     */
    public function allowSerialize(string $propName): bool
    {
        return true;
    }


    /**
     * Checks if this object exists in the current session.
     */
    public function inSession(string $name = ''): bool
    {
        return isset($_SESSION['insession.' . $this->readNamePath()]);
    }

    /**
     * Restores this object from the session.
     *
     * Uses PHP reflection to iterate through published properties
     * and retrieve stored values from the session.
     */
    public function unserialize(): void
    {
        $owner = $this->readOwner();

        if ($owner === null) {
            global $exceptions_enabled;
            if ($exceptions_enabled ?? true) {
                throw new \RuntimeException('Cannot unserialize a component without an owner');
            }
            return;
        }

        // Set loading state if this is a Component
        if ($this instanceof Component) {
            $this->ControlState = CS_LOADING;
        }

        $className = $this->className();
        $namePath = $this->readNamePath();

        // Use method cache for performance
        static $methodCache = [];

        if (!isset($methodCache[$className])) {
            $refClass = new \ReflectionClass($className);
            $methods = $refClass->getMethods();

            // Filter to only setter methods
            $methodCache[$className] = array_values(array_filter(
                array_map(fn($m) => $m->name, $methods),
                fn($name) => str_starts_with($name, 'set')
            ));
        }

        foreach ($methodCache[$className] as $methodName) {
            $propName = substr($methodName, 3);
            $fullName = $namePath . '.' . $propName;

            if (isset($_SESSION[$fullName])) {
                $this->$methodName($_SESSION[$fullName]);
            } else {
                // Try to unserialize sub-objects
                $getter = 'get' . $propName;
                if (method_exists($this, $getter)) {
                    $obj = $this->$getter();
                    if ($obj instanceof Persistent) {
                        $obj->unserialize();
                    }
                }
            }
        }

        // Clear loading state if this is a Component
        if ($this instanceof Component) {
            $this->ControlState = 0;
        }
    }

    /**
     * Type-safe comparison for default value checking.
     */
    protected function typeSafeEqual(mixed $default, mixed $value): bool
    {
        if ($default === $value) {
            return true;
        }

        if ($default == $value) {
            // Handle edge cases for type coercion
            if (is_scalar($default) && $default == 0) {
                if ((is_string($value) && $value === '0') ||
                    (is_bool($value) && $value === false)) {
                    return true;
                }
            }
            if (is_scalar($default) && $default != 0 && is_string($value)) {
                return true;
            }
            if (is_scalar($default) && $default == 1 && is_bool($value) && $value === true) {
                return true;
            }
        }

        return false;
    }
}

