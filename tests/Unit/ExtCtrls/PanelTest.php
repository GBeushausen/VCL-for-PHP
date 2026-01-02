<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\ExtCtrls;

use PHPUnit\Framework\TestCase;
use VCL\ExtCtrls\Panel;

class PanelTest extends TestCase
{
    private Panel $panel;

    protected function setUp(): void
    {
        $this->panel = new Panel();
        $this->panel->Name = 'TestPanel';
    }

    public function testIncludeProperty(): void
    {
        $this->panel->Include = 'header.php';
        $this->assertSame('header.php', $this->panel->Include);
    }

    public function testDynamicProperty(): void
    {
        $this->panel->Dynamic = true;
        $this->assertTrue($this->panel->Dynamic);
    }

    public function testBackgroundProperty(): void
    {
        $this->panel->Background = 'images/bg.png';
        $this->assertSame('images/bg.png', $this->panel->Background);
    }

    public function testBorderWidthProperty(): void
    {
        $this->panel->BorderWidth = 2;
        $this->assertSame(2, $this->panel->BorderWidth);
    }

    public function testBorderWidthRejectsNegative(): void
    {
        $this->panel->BorderWidth = -5;
        $this->assertSame(0, $this->panel->BorderWidth);
    }

    public function testBorderColorProperty(): void
    {
        $this->panel->BorderColor = '#FF0000';
        $this->assertSame('#FF0000', $this->panel->BorderColor);
    }

    public function testBackgroundRepeatProperty(): void
    {
        $this->panel->BackgroundRepeat = 'no-repeat';
        $this->assertSame('no-repeat', $this->panel->BackgroundRepeat);
    }

    public function testBackgroundPositionProperty(): void
    {
        $this->panel->BackgroundPosition = 'center center';
        $this->assertSame('center center', $this->panel->BackgroundPosition);
    }

    public function testActiveLayerProperty(): void
    {
        $this->panel->ActiveLayer = 1;
        $this->assertSame(1, $this->panel->ActiveLayer);
    }
}
