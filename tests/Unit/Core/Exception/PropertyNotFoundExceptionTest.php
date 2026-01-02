<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Core\Exception;

use PHPUnit\Framework\TestCase;
use VCL\Core\Exception\PropertyNotFoundException;

class PropertyNotFoundExceptionTest extends TestCase
{
    public function testIsException(): void
    {
        $exception = new PropertyNotFoundException('MyClass', 'myProperty');
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testMessageContainsClassName(): void
    {
        $exception = new PropertyNotFoundException('TestClass', 'testProp');
        $this->assertStringContainsString('TestClass', $exception->getMessage());
    }

    public function testMessageContainsPropertyName(): void
    {
        $exception = new PropertyNotFoundException('TestClass', 'testProp');
        $this->assertStringContainsString('testProp', $exception->getMessage());
    }

    public function testClassNameProperty(): void
    {
        $exception = new PropertyNotFoundException('MyClass', 'myProperty');
        $this->assertSame('MyClass', $exception->className);
    }

    public function testPropertyNameProperty(): void
    {
        $exception = new PropertyNotFoundException('MyClass', 'myProperty');
        $this->assertSame('myProperty', $exception->propertyName);
    }

    public function testCanBeThrown(): void
    {
        $this->expectException(PropertyNotFoundException::class);
        throw new PropertyNotFoundException('Component', 'UnknownProp');
    }
}
