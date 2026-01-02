<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Core\Exception;

use PHPUnit\Framework\TestCase;
use VCL\Core\Exception\DuplicateNameException;
use VCL\Core\Exception\VCLException;

class DuplicateNameExceptionTest extends TestCase
{
    public function testExtendsVCLException(): void
    {
        $exception = new DuplicateNameException('TestName');
        $this->assertInstanceOf(VCLException::class, $exception);
    }

    public function testMessageContainsName(): void
    {
        $exception = new DuplicateNameException('Button1');
        $this->assertStringContainsString('Button1', $exception->getMessage());
    }

    public function testMessageContainsAlreadyExists(): void
    {
        $exception = new DuplicateNameException('Button1');
        $this->assertStringContainsString('already exists', $exception->getMessage());
    }

    public function testCanBeThrown(): void
    {
        $this->expectException(DuplicateNameException::class);
        throw new DuplicateNameException('DuplicateName');
    }
}
