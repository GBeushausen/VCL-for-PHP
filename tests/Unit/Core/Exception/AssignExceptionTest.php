<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Core\Exception;

use PHPUnit\Framework\TestCase;
use VCL\Core\Exception\AssignException;
use VCL\Core\Exception\VCLException;

class AssignExceptionTest extends TestCase
{
    public function testExtendsVCLException(): void
    {
        $exception = new AssignException('Button', 'Label');
        $this->assertInstanceOf(VCLException::class, $exception);
    }

    public function testMessageContainsSourceName(): void
    {
        $exception = new AssignException('Button', 'Label');
        $this->assertStringContainsString('Button', $exception->getMessage());
    }

    public function testMessageContainsTargetClass(): void
    {
        $exception = new AssignException('Button', 'Label');
        $this->assertStringContainsString('Label', $exception->getMessage());
    }

    public function testMessageContainsCannotAssign(): void
    {
        $exception = new AssignException('Button', 'Label');
        $this->assertStringContainsString('Cannot assign', $exception->getMessage());
    }

    public function testCanBeThrown(): void
    {
        $this->expectException(AssignException::class);
        throw new AssignException('Source', 'Target');
    }

    public function testLegacyAliasExists(): void
    {
        $this->assertTrue(class_exists('EAssignError'));
    }
}
