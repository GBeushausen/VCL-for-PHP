<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Buttons;

use PHPUnit\Framework\TestCase;
use VCL\Buttons\QWidget;

class QWidgetTest extends TestCase
{
    private QWidget $widget;

    protected function setUp(): void
    {
        $this->widget = new QWidget();
        $this->widget->Name = 'TestQWidget';
    }

    public function testDefaultHidden(): void
    {
        $this->assertFalse($this->widget->Hidden);
    }

    public function testHiddenProperty(): void
    {
        $this->widget->Hidden = true;
        $this->assertTrue($this->widget->Hidden);
    }

    public function testIsFocusControl(): void
    {
        $this->assertInstanceOf(\VCL\UI\FocusControl::class, $this->widget);
    }
}
