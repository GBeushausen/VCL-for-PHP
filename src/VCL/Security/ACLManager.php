<?php
/**
 * VCL for PHP
 *
 * Copyright (c) 2004-2008 qadram software S.L.
 * Copyright (c) 2026 Gunnar Beushausen
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 */

declare(strict_types=1);

namespace VCL\Security;

use VCL\Core\Exception\PropertyNotFoundException;

/**
 * Manager class for ACL (Access Control List) system.
 *
 * This class holds a list of all ACL objects used to control resource access.
 * Components automatically register themselves as resources.
 *
 * Example usage:
 * ```php
 * $aclManager = ACLManager::getInstance();
 * $aclManager->Role = 'admin';
 * $aclManager->addACL(new MyACLImplementation());
 *
 * // Check if current role can access a resource
 * if ($aclManager->isAllowed(null, 'Page::MyPage', 'view')) {
 *     // Allow access
 * }
 * ```
 */
class ACLManager
{
    private static ?ACLManager $instance = null;

    /** @var ACLInterface[] */
    private array $aclObjects = [];

    protected string $_role = '';

    // =========================================================================
    // SINGLETON
    // =========================================================================

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    // =========================================================================
    // PROPERTY HOOKS
    // =========================================================================

    public string $Role {
        get => $this->_role;
        set => $this->_role = $value;
    }

    // =========================================================================
    // MAGIC METHODS (Legacy compatibility)
    // =========================================================================

    public function __get(string $name): mixed
    {
        $method = 'get' . $name;
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        $method = 'read' . $name;
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new PropertyNotFoundException(static::class, $name);
    }

    public function __set(string $name, mixed $value): void
    {
        $method = 'set' . $name;
        if (method_exists($this, $method)) {
            $this->$method($value);
            return;
        }

        $method = 'write' . $name;
        if (method_exists($this, $method)) {
            $this->$method($value);
            return;
        }

        throw new PropertyNotFoundException(static::class, $name);
    }

    // =========================================================================
    // PUBLIC METHODS
    // =========================================================================

    /**
     * Adds an ACL object to the authentication chain.
     *
     * @param ACLInterface $acl ACL object to add
     */
    public function addACL(ACLInterface $acl): void
    {
        $this->aclObjects[] = $acl;
    }

    /**
     * Remove an ACL object from the chain.
     *
     * @param ACLInterface $acl ACL object to remove
     */
    public function removeACL(ACLInterface $acl): void
    {
        $key = array_search($acl, $this->aclObjects, true);
        if ($key !== false) {
            unset($this->aclObjects[$key]);
            $this->aclObjects = array_values($this->aclObjects);
        }
    }

    /**
     * Clear all ACL objects.
     */
    public function clearACLs(): void
    {
        $this->aclObjects = [];
    }

    /**
     * Check if a role is allowed to access a resource with a privilege.
     *
     * Iterates through all ACL objects until one returns true.
     * If no ACL objects are registered, returns true (allow all).
     *
     * @param string|null $role The role to check (null uses current Role)
     * @param string|null $resource The resource identifier
     * @param string|null $privilege The privilege/action to check
     * @return bool True if allowed
     */
    public function isAllowed(?string $role = null, ?string $resource = null, ?string $privilege = null): bool
    {
        // No ACL objects registered = all actions allowed
        if (count($this->aclObjects) === 0) {
            return true;
        }

        $role = $role ?? $this->_role;

        foreach ($this->aclObjects as $acl) {
            if ($acl->isAllowed($role, $resource, $privilege)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add a resource to all registered ACL objects.
     *
     * @param string $resourceName Resource identifier
     */
    public function addResource(string $resourceName): void
    {
        foreach ($this->aclObjects as $acl) {
            $acl->addResource($resourceName);
        }
    }

    /**
     * Get the number of registered ACL objects.
     */
    public function getACLCount(): int
    {
        return count($this->aclObjects);
    }

    // =========================================================================
    // LEGACY COMPATIBILITY METHODS
    // =========================================================================

    public function getRole(): string
    {
        return $this->_role;
    }

    public function setRole(string $value): void
    {
        $this->_role = $value;
    }

    public function defaultRole(): string
    {
        return '';
    }
}

// =========================================================================
// GLOBAL FUNCTIONS (Legacy compatibility)
// =========================================================================

/**
 * Add a resource to the ACL system.
 *
 * @param object|string $object Object or string identifier for the resource
 */
function acl_addresource(object|string $object): void
{
    $manager = ACLManager::getInstance();

    if (is_object($object)) {
        $className = $object::class;
        $name = $object->Name ?? '';
        $manager->addResource($className . '::' . $name);
    } else {
        $manager->addResource($object);
    }
}

/**
 * Check if the current role can access a resource with a privilege.
 *
 * @param string|null $resource Resource identifier
 * @param string|null $privilege Privilege/action to check
 * @return bool True if allowed
 */
function acl_isallowed(?string $resource = null, ?string $privilege = null): bool
{
    $manager = ACLManager::getInstance();
    return $manager->isAllowed($manager->Role, $resource, $privilege);
}
