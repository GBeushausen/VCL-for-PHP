<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Auth;

use PHPUnit\Framework\TestCase;
use VCL\Auth\User;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
        $this->user->Name = 'TestUser';
    }

    public function testDefaultLoggedIsFalse(): void
    {
        $this->assertFalse($this->user->Logged);
    }

    public function testLoggedProperty(): void
    {
        $this->user->Logged = true;
        $this->assertTrue($this->user->Logged);
    }

    public function testAuthenticateReturnsFalseByDefault(): void
    {
        $result = $this->user->authenticate('user', 'password');
        $this->assertFalse($result);
    }

    public function testIsComponent(): void
    {
        $this->assertInstanceOf(\VCL\Core\Component::class, $this->user);
    }
}
