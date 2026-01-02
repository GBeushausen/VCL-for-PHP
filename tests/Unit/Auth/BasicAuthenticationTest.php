<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Auth;

use PHPUnit\Framework\TestCase;
use VCL\Auth\BasicAuthentication;

class BasicAuthenticationTest extends TestCase
{
    private BasicAuthentication $auth;

    protected function setUp(): void
    {
        $this->auth = new BasicAuthentication();
        $this->auth->Name = 'TestBasicAuth';
    }

    public function testDefaultTitle(): void
    {
        $this->assertSame('Login', $this->auth->Title);
    }

    public function testTitleProperty(): void
    {
        $this->auth->Title = 'Admin Area';
        $this->assertSame('Admin Area', $this->auth->Title);
    }

    public function testDefaultErrorMessage(): void
    {
        $this->assertSame('Unauthorized', $this->auth->ErrorMessage);
    }

    public function testErrorMessageProperty(): void
    {
        $this->auth->ErrorMessage = 'Access Denied';
        $this->assertSame('Access Denied', $this->auth->ErrorMessage);
    }

    public function testUsernameProperty(): void
    {
        $this->auth->Username = 'admin';
        $this->assertSame('admin', $this->auth->Username);
    }

    public function testPasswordProperty(): void
    {
        $this->auth->Password = 'secret123';
        $this->assertSame('secret123', $this->auth->Password);
    }

    public function testOnAuthenticateEvent(): void
    {
        $this->auth->OnAuthenticate = 'handleAuth';
        $this->assertSame('handleAuth', $this->auth->OnAuthenticate);
    }

    public function testIsNotAuthenticatedByDefault(): void
    {
        $this->assertFalse($this->auth->isAuthenticated());
    }

    public function testGetAuthenticatedUsernameReturnsNull(): void
    {
        $this->assertNull($this->auth->getAuthenticatedUsername());
    }

    public function testIsComponent(): void
    {
        $this->assertInstanceOf(\VCL\Core\Component::class, $this->auth);
    }
}
