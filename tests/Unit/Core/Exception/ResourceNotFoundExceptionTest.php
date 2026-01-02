<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Core\Exception;

use PHPUnit\Framework\TestCase;
use VCL\Core\Exception\ResourceNotFoundException;
use VCL\Core\Exception\VCLException;

class ResourceNotFoundExceptionTest extends TestCase
{
    public function testExtendsVCLException(): void
    {
        $exception = new ResourceNotFoundException('myform.xml.php');
        $this->assertInstanceOf(VCLException::class, $exception);
    }

    public function testMessageContainsResourceName(): void
    {
        $exception = new ResourceNotFoundException('myform.xml.php');
        $this->assertStringContainsString('myform.xml.php', $exception->getMessage());
    }

    public function testMessageContainsNotFound(): void
    {
        $exception = new ResourceNotFoundException('resource.xml');
        $this->assertStringContainsString('not found', $exception->getMessage());
    }

    public function testCanBeThrown(): void
    {
        $this->expectException(ResourceNotFoundException::class);
        throw new ResourceNotFoundException('missing.xml');
    }

    public function testLegacyAliasExists(): void
    {
        $this->assertTrue(class_exists('EResNotFound'));
    }
}
