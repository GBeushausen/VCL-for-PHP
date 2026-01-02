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

/**
 * Simple in-memory ACL implementation.
 *
 * Provides a basic ACL that stores rules in memory.
 *
 * Example usage:
 * ```php
 * $acl = new SimpleACL();
 *
 * // Add resources
 * $acl->addResource('Page::AdminPage');
 * $acl->addResource('Page::UserPage');
 *
 * // Add roles
 * $acl->addRole('guest');
 * $acl->addRole('user');
 * $acl->addRole('admin');
 *
 * // Set permissions
 * $acl->allow('admin', 'Page::AdminPage', 'view');
 * $acl->allow('user', 'Page::UserPage', 'view');
 * $acl->allow('user', 'Page::UserPage', 'edit');
 *
 * // Check permissions
 * $acl->isAllowed('admin', 'Page::AdminPage', 'view'); // true
 * $acl->isAllowed('guest', 'Page::AdminPage', 'view'); // false
 * ```
 */
class SimpleACL implements ACLInterface
{
    /** @var string[] */
    private array $resources = [];

    /** @var string[] */
    private array $roles = [];

    /**
     * @var array<string, array<string, array<string, bool>>>
     * Structure: [role][resource][privilege] = allowed
     */
    private array $rules = [];

    // =========================================================================
    // ACLInterface IMPLEMENTATION
    // =========================================================================

    /**
     * Check if a role is allowed to access a resource with a privilege.
     */
    public function isAllowed(?string $role, ?string $resource, ?string $privilege): bool
    {
        if ($role === null || $resource === null) {
            return false;
        }

        // Check specific rule
        if (isset($this->rules[$role][$resource][$privilege])) {
            return $this->rules[$role][$resource][$privilege];
        }

        // Check wildcard privilege
        if (isset($this->rules[$role][$resource]['*'])) {
            return $this->rules[$role][$resource]['*'];
        }

        // Check wildcard resource
        if (isset($this->rules[$role]['*'][$privilege])) {
            return $this->rules[$role]['*'][$privilege];
        }

        // Check full wildcard
        if (isset($this->rules[$role]['*']['*'])) {
            return $this->rules[$role]['*']['*'];
        }

        return false;
    }

    /**
     * Add a resource to this ACL.
     */
    public function addResource(string $resourceName): void
    {
        if (!in_array($resourceName, $this->resources, true)) {
            $this->resources[] = $resourceName;
        }
    }

    // =========================================================================
    // PUBLIC METHODS
    // =========================================================================

    /**
     * Add a role to this ACL.
     */
    public function addRole(string $role): void
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    /**
     * Allow a role to access a resource with a privilege.
     *
     * @param string $role Role name
     * @param string $resource Resource identifier (use '*' for all resources)
     * @param string $privilege Privilege/action (use '*' for all privileges)
     */
    public function allow(string $role, string $resource = '*', string $privilege = '*'): void
    {
        if (!isset($this->rules[$role])) {
            $this->rules[$role] = [];
        }
        if (!isset($this->rules[$role][$resource])) {
            $this->rules[$role][$resource] = [];
        }
        $this->rules[$role][$resource][$privilege] = true;
    }

    /**
     * Deny a role access to a resource with a privilege.
     *
     * @param string $role Role name
     * @param string $resource Resource identifier (use '*' for all resources)
     * @param string $privilege Privilege/action (use '*' for all privileges)
     */
    public function deny(string $role, string $resource = '*', string $privilege = '*'): void
    {
        if (!isset($this->rules[$role])) {
            $this->rules[$role] = [];
        }
        if (!isset($this->rules[$role][$resource])) {
            $this->rules[$role][$resource] = [];
        }
        $this->rules[$role][$resource][$privilege] = false;
    }

    /**
     * Remove a specific rule.
     */
    public function removeRule(string $role, string $resource = '*', string $privilege = '*'): void
    {
        unset($this->rules[$role][$resource][$privilege]);
    }

    /**
     * Remove all rules for a role.
     */
    public function removeRoleRules(string $role): void
    {
        unset($this->rules[$role]);
    }

    /**
     * Clear all rules.
     */
    public function clearRules(): void
    {
        $this->rules = [];
    }

    /**
     * Get all registered resources.
     *
     * @return string[]
     */
    public function getResources(): array
    {
        return $this->resources;
    }

    /**
     * Get all registered roles.
     *
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Check if a resource exists.
     */
    public function hasResource(string $resourceName): bool
    {
        return in_array($resourceName, $this->resources, true);
    }

    /**
     * Check if a role exists.
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }
}
