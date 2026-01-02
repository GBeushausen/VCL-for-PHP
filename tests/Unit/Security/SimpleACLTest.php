<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Security;

use PHPUnit\Framework\TestCase;
use VCL\Security\SimpleACL;

class SimpleACLTest extends TestCase
{
    private SimpleACL $acl;

    protected function setUp(): void
    {
        $this->acl = new SimpleACL();
    }

    public function testAddResource(): void
    {
        $this->acl->addResource('Page::AdminPage');
        $this->assertTrue($this->acl->hasResource('Page::AdminPage'));
    }

    public function testAddRole(): void
    {
        $this->acl->addRole('admin');
        $this->assertTrue($this->acl->hasRole('admin'));
    }

    public function testAllowAndIsAllowed(): void
    {
        $this->acl->addRole('admin');
        $this->acl->addResource('Page::AdminPage');
        $this->acl->allow('admin', 'Page::AdminPage', 'view');
        $this->assertTrue($this->acl->isAllowed('admin', 'Page::AdminPage', 'view'));
    }

    public function testDenyOverridesAllow(): void
    {
        $this->acl->addRole('user');
        $this->acl->addResource('Page::AdminPage');
        $this->acl->allow('user', 'Page::AdminPage', 'view');
        $this->acl->deny('user', 'Page::AdminPage', 'view');
        $this->assertFalse($this->acl->isAllowed('user', 'Page::AdminPage', 'view'));
    }

    public function testIsAllowedReturnsFalseByDefault(): void
    {
        $this->acl->addRole('guest');
        $this->acl->addResource('Page::AdminPage');
        $this->assertFalse($this->acl->isAllowed('guest', 'Page::AdminPage', 'view'));
    }

    public function testWildcardPrivilege(): void
    {
        $this->acl->addRole('admin');
        $this->acl->addResource('Page::AdminPage');
        $this->acl->allow('admin', 'Page::AdminPage', '*');
        $this->assertTrue($this->acl->isAllowed('admin', 'Page::AdminPage', 'view'));
        $this->assertTrue($this->acl->isAllowed('admin', 'Page::AdminPage', 'edit'));
    }

    public function testWildcardResource(): void
    {
        $this->acl->addRole('superadmin');
        $this->acl->allow('superadmin', '*', 'view');
        $this->assertTrue($this->acl->isAllowed('superadmin', 'AnyResource', 'view'));
    }

    public function testRemoveRule(): void
    {
        $this->acl->addRole('admin');
        $this->acl->allow('admin', 'resource', 'view');
        $this->acl->removeRule('admin', 'resource', 'view');
        $this->assertFalse($this->acl->isAllowed('admin', 'resource', 'view'));
    }

    public function testClearRules(): void
    {
        $this->acl->addRole('admin');
        $this->acl->allow('admin', 'resource', 'view');
        $this->acl->clearRules();
        $this->assertFalse($this->acl->isAllowed('admin', 'resource', 'view'));
    }

    public function testGetResources(): void
    {
        $this->acl->addResource('Resource1');
        $this->acl->addResource('Resource2');
        $resources = $this->acl->getResources();
        $this->assertCount(2, $resources);
        $this->assertContains('Resource1', $resources);
        $this->assertContains('Resource2', $resources);
    }

    public function testGetRoles(): void
    {
        $this->acl->addRole('admin');
        $this->acl->addRole('user');
        $roles = $this->acl->getRoles();
        $this->assertCount(2, $roles);
        $this->assertContains('admin', $roles);
        $this->assertContains('user', $roles);
    }

    public function testImplementsACLInterface(): void
    {
        $this->assertInstanceOf(\VCL\Security\ACLInterface::class, $this->acl);
    }
}
