<?php
/**
 * VCL for PHP 3.0
 *
 * Unit tests for Component class
 */

declare(strict_types=1);

namespace VCL\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use VCL\Core\Component;

class ComponentTest extends TestCase
{
    private Component $component;

    protected function setUp(): void
    {
        $this->component = new Component();
    }

    public function testNameProperty(): void
    {
        $this->component->Name = 'TestComponent';
        $this->assertSame('TestComponent', $this->component->Name);
    }

    public function testTagProperty(): void
    {
        $this->component->Tag = 42;
        $this->assertSame(42, $this->component->Tag);
    }

    public function testOwnerIsNullByDefault(): void
    {
        $this->assertNull($this->component->Owner);
    }

    public function testComponentWithOwner(): void
    {
        $owner = new Component();
        $owner->Name = 'Owner';

        $child = new Component($owner);
        $child->Name = 'Child';

        $this->assertSame($owner, $child->Owner);
    }

    public function testComponentIsInstanceOfVCLObject(): void
    {
        $this->assertInstanceOf(\VCL\Core\VCLObject::class, $this->component);
    }
}
