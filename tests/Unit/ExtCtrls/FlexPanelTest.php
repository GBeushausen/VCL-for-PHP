<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\ExtCtrls;

use PHPUnit\Framework\TestCase;
use VCL\ExtCtrls\FlexPanel;
use VCL\UI\Enums\FlexDirection;
use VCL\UI\Enums\FlexWrap;
use VCL\UI\Enums\JustifyContent;
use VCL\UI\Enums\AlignItems;
use VCL\UI\Enums\RenderMode;

class FlexPanelTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $panel = new FlexPanel();
        $panel->Name = 'TestPanel';

        $this->assertSame(FlexDirection::Row, $panel->Direction);
        $this->assertSame(FlexWrap::NoWrap, $panel->Wrap);
        $this->assertSame(JustifyContent::Start, $panel->JustifyContent);
        $this->assertSame(AlignItems::Stretch, $panel->AlignItems);
        $this->assertSame('gap-4', $panel->FlexGap);
        $this->assertSame(RenderMode::Tailwind, $panel->RenderMode);
    }

    public function testDirectionProperty(): void
    {
        $panel = new FlexPanel();
        $panel->Name = 'TestPanel';
        $panel->Direction = FlexDirection::Column;

        $this->assertSame(FlexDirection::Column, $panel->Direction);
    }

    public function testWrapProperty(): void
    {
        $panel = new FlexPanel();
        $panel->Name = 'TestPanel';
        $panel->Wrap = FlexWrap::Wrap;

        $this->assertSame(FlexWrap::Wrap, $panel->Wrap);
    }

    public function testJustifyContentProperty(): void
    {
        $panel = new FlexPanel();
        $panel->Name = 'TestPanel';
        $panel->JustifyContent = JustifyContent::Center;

        $this->assertSame(JustifyContent::Center, $panel->JustifyContent);
    }

    public function testAlignItemsProperty(): void
    {
        $panel = new FlexPanel();
        $panel->Name = 'TestPanel';
        $panel->AlignItems = AlignItems::Center;

        $this->assertSame(AlignItems::Center, $panel->AlignItems);
    }

    public function testFlexGapProperty(): void
    {
        $panel = new FlexPanel();
        $panel->Name = 'TestPanel';
        $panel->FlexGap = 'gap-8';

        $this->assertSame('gap-8', $panel->FlexGap);
    }

    public function testResponsiveDirectionProperty(): void
    {
        $panel = new FlexPanel();
        $panel->Name = 'TestPanel';
        $panel->ResponsiveDirection = ['md' => FlexDirection::Row];

        $this->assertSame(['md' => FlexDirection::Row], $panel->ResponsiveDirection);
    }

    public function testRender(): void
    {
        $panel = new FlexPanel();
        $panel->Name = 'TestFlexPanel';
        $panel->Direction = FlexDirection::Column;
        $panel->JustifyContent = JustifyContent::Center;

        $output = $panel->render();

        $this->assertStringContainsString('id="TestFlexPanel"', $output);
        $this->assertStringContainsString('flex', $output);
        $this->assertStringContainsString('flex-col', $output);
        $this->assertStringContainsString('justify-center', $output);
    }
}
