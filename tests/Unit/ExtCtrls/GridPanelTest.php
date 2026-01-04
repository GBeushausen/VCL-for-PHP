<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\ExtCtrls;

use PHPUnit\Framework\TestCase;
use VCL\ExtCtrls\GridPanel;
use VCL\UI\Enums\RenderMode;

class GridPanelTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $panel = new GridPanel();
        $panel->Name = 'TestPanel';

        $this->assertSame(1, $panel->Columns);
        $this->assertSame('gap-4', $panel->GridGap);
        $this->assertSame('', $panel->RowGap);
        $this->assertSame('', $panel->ColGap);
        $this->assertSame(RenderMode::Tailwind, $panel->RenderMode);
    }

    public function testColumnsProperty(): void
    {
        $panel = new GridPanel();
        $panel->Name = 'TestPanel';
        $panel->Columns = 3;

        $this->assertSame(3, $panel->Columns);
    }

    public function testColumnsMinimumIsOne(): void
    {
        $panel = new GridPanel();
        $panel->Name = 'TestPanel';
        $panel->Columns = 0;

        $this->assertSame(1, $panel->Columns);
    }

    public function testResponsiveColumnsProperty(): void
    {
        $panel = new GridPanel();
        $panel->Name = 'TestPanel';
        $panel->ResponsiveColumns = ['sm' => 2, 'md' => 3, 'lg' => 4];

        $this->assertSame(['sm' => 2, 'md' => 3, 'lg' => 4], $panel->ResponsiveColumns);
    }

    public function testGridGapProperty(): void
    {
        $panel = new GridPanel();
        $panel->Name = 'TestPanel';
        $panel->GridGap = 'gap-8';

        $this->assertSame('gap-8', $panel->GridGap);
    }

    public function testRowGapProperty(): void
    {
        $panel = new GridPanel();
        $panel->Name = 'TestPanel';
        $panel->RowGap = 'gap-y-4';

        $this->assertSame('gap-y-4', $panel->RowGap);
    }

    public function testColGapProperty(): void
    {
        $panel = new GridPanel();
        $panel->Name = 'TestPanel';
        $panel->ColGap = 'gap-x-6';

        $this->assertSame('gap-x-6', $panel->ColGap);
    }

    public function testRender(): void
    {
        $panel = new GridPanel();
        $panel->Name = 'TestGridPanel';
        $panel->Columns = 3;
        $panel->ResponsiveColumns = ['sm' => 2];

        $output = $panel->render();

        $this->assertStringContainsString('id="TestGridPanel"', $output);
        $this->assertStringContainsString('grid', $output);
        $this->assertStringContainsString('grid-cols-3', $output);
        $this->assertStringContainsString('sm:grid-cols-2', $output);
    }
}
