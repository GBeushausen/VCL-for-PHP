<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Database;

use PHPUnit\Framework\TestCase;
use VCL\Database\EDatabaseError;
use VCL\Core\Component;

class EDatabaseErrorTest extends TestCase
{
    public function testExtendsException(): void
    {
        $exception = new EDatabaseError('Test error');
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testMessage(): void
    {
        $exception = new EDatabaseError('Database connection failed');
        $this->assertSame('Database connection failed', $exception->getMessage());
    }

    public function testRaiseWithoutComponent(): void
    {
        $this->expectException(EDatabaseError::class);
        $this->expectExceptionMessage('Test database error');

        EDatabaseError::raise('Test database error');
    }

    public function testRaiseWithComponent(): void
    {
        $component = new Component();
        $component->Name = 'TestDB';

        $this->expectException(EDatabaseError::class);
        $this->expectExceptionMessage('TestDB: Connection failed');

        EDatabaseError::raise('Connection failed', $component);
    }

    public function testRaiseWithAnonymousComponent(): void
    {
        $component = new Component();
        // No name set

        $this->expectException(EDatabaseError::class);
        $this->expectExceptionMessage('Simple error');

        EDatabaseError::raise('Simple error', $component);
    }
}
