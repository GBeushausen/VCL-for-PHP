<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use VCL\Core\VCLObject;

class VCLObjectTest extends TestCase
{
    private VCLObject $object;

    protected function setUp(): void
    {
        $this->object = new VCLObject();
    }

    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(VCLObject::class, $this->object);
    }

    public function testClassNameIs(): void
    {
        $this->assertTrue($this->object->classNameIs('VCLObject'));
        $this->assertTrue($this->object->classNameIs('vclobject'));
        $this->assertFalse($this->object->classNameIs('SomeOtherClass'));
    }

    public function testInheritsFrom(): void
    {
        $this->assertTrue($this->object->inheritsFrom('VCL\Core\VCLObject'));
    }
}
