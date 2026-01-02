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
 * Interface for ACL implementations.
 *
 * Implement this interface to create custom ACL providers that can be
 * registered with the ACLManager.
 */
interface ACLInterface
{
    /**
     * Check if a role is allowed to access a resource with a privilege.
     *
     * @param string|null $role The role to check
     * @param string|null $resource The resource identifier
     * @param string|null $privilege The privilege/action to check
     * @return bool True if allowed
     */
    public function isAllowed(?string $role, ?string $resource, ?string $privilege): bool;

    /**
     * Add a resource to this ACL.
     *
     * @param string $resourceName Resource identifier
     */
    public function addResource(string $resourceName): void;
}
