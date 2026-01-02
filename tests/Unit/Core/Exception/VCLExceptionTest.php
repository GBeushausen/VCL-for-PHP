<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Core\Exception;

use PHPUnit\Framework\TestCase;
use VCL\Core\Exception\VCLException;

class VCLExceptionTest extends TestCase
{
    public function testIsException(): void
    {
        $exception = new VCLException('Test error');
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testMessageIsSet(): void
    {
        $exception = new VCLException('Test error message');
        $this->assertSame('Test error message', $exception->getMessage());
    }

    public function testCodeIsSet(): void
    {
        $exception = new VCLException('Error', 42);
        $this->assertSame(42, $exception->getCode());
    }

    public function testCanBeThrown(): void
    {
        $this->expectException(VCLException::class);
        $this->expectExceptionMessage('Something went wrong');
        throw new VCLException('Something went wrong');
    }
}
