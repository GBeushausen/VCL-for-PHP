<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Security;

use PHPUnit\Framework\TestCase;
use VCL\Security\ACLManager;
use VCL\Security\SimpleACL;

class ACLManagerTest extends TestCase
{
    protected function setUp(): void
    {
        // Reset the singleton for each test
        $reflection = new \ReflectionClass(ACLManager::class);
        $instance = $reflection->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null, null);
    }

    public function testGetInstance(): void
    {
        $manager = ACLManager::getInstance();
        $this->assertInstanceOf(ACLManager::class, $manager);
    }

    public function testGetInstanceReturnsSameInstance(): void
    {
        $manager1 = ACLManager::getInstance();
        $manager2 = ACLManager::getInstance();
        $this->assertSame($manager1, $manager2);
    }

    public function testAddACL(): void
    {
        $manager = ACLManager::getInstance();
        $acl = new SimpleACL();

        $manager->addACL($acl);
        $this->assertSame(1, $manager->getACLCount());
    }

    public function testClearACLs(): void
    {
        $manager = ACLManager::getInstance();
        $manager->addACL(new SimpleACL());
        $manager->addACL(new SimpleACL());

        $this->assertSame(2, $manager->getACLCount());

        $manager->clearACLs();
        $this->assertSame(0, $manager->getACLCount());
    }

    public function testRoleProperty(): void
    {
        $manager = ACLManager::getInstance();
        $manager->Role = 'admin';
        $this->assertSame('admin', $manager->Role);
    }

    public function testIsAllowedDelegatesToACL(): void
    {
        $manager = ACLManager::getInstance();
        $acl = new SimpleACL();

        $acl->addRole('admin');
        $acl->addResource('Page::Dashboard');
        $acl->allow('admin', 'Page::Dashboard', 'view');

        $manager->addACL($acl);

        $this->assertTrue($manager->isAllowed('admin', 'Page::Dashboard', 'view'));
        $this->assertFalse($manager->isAllowed('admin', 'Page::Dashboard', 'delete'));
    }

    public function testIsAllowedReturnsTrueWhenNoACL(): void
    {
        $manager = ACLManager::getInstance();
        // When no ACL is registered, all actions are allowed
        $this->assertTrue($manager->isAllowed('admin', 'resource', 'privilege'));
    }

    public function testIsAllowedUsesCurrentRoleWhenNull(): void
    {
        $manager = ACLManager::getInstance();
        $acl = new SimpleACL();

        $acl->addRole('user');
        $acl->addResource('Page::Home');
        $acl->allow('user', 'Page::Home', 'view');

        $manager->addACL($acl);
        $manager->Role = 'user';

        $this->assertTrue($manager->isAllowed(null, 'Page::Home', 'view'));
    }
}
