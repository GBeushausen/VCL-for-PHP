<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\RTL;

use PHPUnit\Framework\TestCase;
use VCL\RTL\AbortException;

class AbortExceptionTest extends TestCase
{
    public function testExtendsException(): void
    {
        $exception = new AbortException();
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testDefaultMessage(): void
    {
        $exception = new AbortException();
        $this->assertSame('Operation aborted', $exception->getMessage());
    }

    public function testCustomMessage(): void
    {
        $exception = new AbortException('Custom abort message');
        $this->assertSame('Custom abort message', $exception->getMessage());
    }

    public function testCanBeThrown(): void
    {
        $this->expectException(AbortException::class);
        throw new AbortException();
    }

    public function testCanBeThrownWithMessage(): void
    {
        $this->expectException(AbortException::class);
        $this->expectExceptionMessage('Test abort');
        throw new AbortException('Test abort');
    }

    public function testLegacyAliasExists(): void
    {
        $this->assertTrue(class_exists('EAbort'));
    }
}
