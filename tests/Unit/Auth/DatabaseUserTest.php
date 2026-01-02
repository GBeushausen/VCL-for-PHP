<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Auth;

use PHPUnit\Framework\TestCase;
use VCL\Auth\DatabaseUser;

class DatabaseUserTest extends TestCase
{
    private DatabaseUser $user;

    protected function setUp(): void
    {
        $this->user = new DatabaseUser();
        $this->user->Name = 'TestDatabaseUser';
    }

    public function testDriverNameProperty(): void
    {
        $this->user->DriverName = 'mysql';
        $this->assertSame('mysql', $this->user->DriverName);
    }

    public function testDatabaseNameProperty(): void
    {
        $this->user->DatabaseName = 'test_db';
        $this->assertSame('test_db', $this->user->DatabaseName);
    }

    public function testHostProperty(): void
    {
        $this->user->Host = 'localhost';
        $this->assertSame('localhost', $this->user->Host);
    }

    public function testUserProperty(): void
    {
        $this->user->User = 'dbuser';
        $this->assertSame('dbuser', $this->user->User);
    }

    public function testPasswordProperty(): void
    {
        $this->user->Password = 'secret';
        $this->assertSame('secret', $this->user->Password);
    }

    public function testUsersTableProperty(): void
    {
        $this->user->UsersTable = 'users';
        $this->assertSame('users', $this->user->UsersTable);
    }

    public function testUserNameFieldNameProperty(): void
    {
        $this->user->UserNameFieldName = 'username';
        $this->assertSame('username', $this->user->UserNameFieldName);
    }

    public function testPasswordFieldNameProperty(): void
    {
        $this->user->PasswordFieldName = 'password_hash';
        $this->assertSame('password_hash', $this->user->PasswordFieldName);
    }

    public function testLoggedPropertyDefaultFalse(): void
    {
        $this->assertFalse($this->user->Logged);
    }

    public function testExtendsUser(): void
    {
        $this->assertInstanceOf(\VCL\Auth\User::class, $this->user);
    }

    public function testIsComponent(): void
    {
        $this->assertInstanceOf(\VCL\Core\Component::class, $this->user);
    }
}
