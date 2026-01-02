<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\UI;

use PHPUnit\Framework\TestCase;
use VCL\UI\FocusControl;
use VCL\Graphics\Layout;

class FocusControlTest extends TestCase
{
    private FocusControl $control;

    protected function setUp(): void
    {
        $this->control = new FocusControl();
        $this->control->Name = 'TestFocusControl';
    }

    public function testLayoutProperty(): void
    {
        $layout = $this->control->Layout;
        $this->assertInstanceOf(Layout::class, $layout);
    }

    public function testLayoutIsCreatedAutomatically(): void
    {
        $this->assertNotNull($this->control->Layout);
    }

    public function testControlCountDefault(): void
    {
        $this->assertSame(0, $this->control->ControlCount);
    }

    public function testUpdateChildrenFonts(): void
    {
        // Should not throw when called with no children
        $this->control->updateChildrenFonts();
        $this->assertTrue(true);
    }

    public function testUpdateChildrenColors(): void
    {
        // Should not throw when called with no children
        $this->control->updateChildrenColors();
        $this->assertTrue(true);
    }

    public function testUpdateChildrenShowHints(): void
    {
        // Should not throw when called with no children
        $this->control->updateChildrenShowHints();
        $this->assertTrue(true);
    }

    public function testDumpChildren(): void
    {
        // Should not throw when called with no children
        $this->control->dumpChildren();
        $this->assertTrue(true);
    }

    public function testIsControl(): void
    {
        $this->assertInstanceOf(\VCL\UI\Control::class, $this->control);
    }
}
