<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Charts;

use PHPUnit\Framework\TestCase;
use VCL\Charts\Chart;
use const VCL\Charts\CT_BAR;
use const VCL\Charts\CT_LINE;
use const VCL\Charts\CT_PIE;

class ChartTest extends TestCase
{
    private Chart $chart;

    protected function setUp(): void
    {
        $this->chart = new Chart();
        $this->chart->Name = 'TestChart';
    }

    public function testDefaultChartType(): void
    {
        $this->assertSame(CT_BAR, $this->chart->ChartType);
    }

    public function testChartTypeProperty(): void
    {
        $this->chart->ChartType = CT_LINE;
        $this->assertSame(CT_LINE, $this->chart->ChartType);
    }

    public function testDefaultDimensions(): void
    {
        $this->assertSame(400, $this->chart->Width);
        $this->assertSame(300, $this->chart->Height);
    }

    public function testTitleProperty(): void
    {
        $this->assertSame('', $this->chart->Title);
        $this->chart->Title = 'Sales Chart';
        $this->assertSame('Sales Chart', $this->chart->Title);
    }

    public function testLabelsProperty(): void
    {
        $this->assertSame([], $this->chart->Labels);
        $labels = ['Q1', 'Q2', 'Q3', 'Q4'];
        $this->chart->Labels = $labels;
        $this->assertSame($labels, $this->chart->Labels);
    }

    public function testDefaultLegend(): void
    {
        $this->assertTrue($this->chart->Legend);
    }

    public function testLegendProperty(): void
    {
        $this->chart->Legend = false;
        $this->assertFalse($this->chart->Legend);
    }

    public function testDefaultResponsive(): void
    {
        $this->assertFalse($this->chart->Responsive);
    }

    public function testResponsiveProperty(): void
    {
        $this->chart->Responsive = true;
        $this->assertTrue($this->chart->Responsive);
    }

    public function testMaintainAspectRatioProperty(): void
    {
        $this->assertTrue($this->chart->MaintainAspectRatio);
        $this->chart->MaintainAspectRatio = false;
        $this->assertFalse($this->chart->MaintainAspectRatio);
    }

    public function testChartJsUrlProperty(): void
    {
        $this->assertStringContainsString('chart.js', $this->chart->ChartJsUrl);
        $this->chart->ChartJsUrl = '/js/chart.min.js';
        $this->assertSame('/js/chart.min.js', $this->chart->ChartJsUrl);
    }

    public function testAddDataset(): void
    {
        $this->chart->Labels = ['Jan', 'Feb', 'Mar'];
        $this->chart->addDataset('2024', [10, 20, 30], '#FF0000');

        // Verify by checking that chart renders without error
        $output = $this->chart->render();

        $this->assertStringContainsString('TestChart', $output);
        $this->assertStringContainsString('canvas', $output);
    }

    public function testAddPoint(): void
    {
        $this->chart->addPoint('January', 100, '#0000FF');
        $this->chart->addPoint('February', 150, '#00FF00');

        $this->assertCount(2, $this->chart->Labels);
        $this->assertSame('January', $this->chart->Labels[0]);
    }

    public function testClearData(): void
    {
        $this->chart->Labels = ['A', 'B'];
        $this->chart->addDataset('Test', [1, 2]);

        $this->chart->clearData();

        $this->assertEmpty($this->chart->Labels);
    }

    public function testIsControl(): void
    {
        $this->assertInstanceOf(\VCL\UI\Control::class, $this->chart);
    }
}
