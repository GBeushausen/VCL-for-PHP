<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Core\Exception;

use PHPUnit\Framework\TestCase;
use VCL\Core\Exception\CollectionException;
use VCL\Core\Exception\VCLException;

class CollectionExceptionTest extends TestCase
{
    public function testExtendsVCLException(): void
    {
        $exception = new CollectionException(5);
        $this->assertInstanceOf(VCLException::class, $exception);
    }

    public function testMessageContainsIndex(): void
    {
        $exception = new CollectionException(42);
        $this->assertStringContainsString('42', $exception->getMessage());
    }

    public function testMessageContainsOutOfBounds(): void
    {
        $exception = new CollectionException(5);
        $this->assertStringContainsString('out of bounds', $exception->getMessage());
    }

    public function testAcceptsStringIndex(): void
    {
        $exception = new CollectionException('invalid_key');
        $this->assertStringContainsString('invalid_key', $exception->getMessage());
    }

    public function testCanBeThrown(): void
    {
        $this->expectException(CollectionException::class);
        throw new CollectionException(10);
    }

    public function testLegacyAliasExists(): void
    {
        $this->assertTrue(class_exists('ECollectionError'));
    }
}
