<?php
/**
 * VCL for PHP
 *
 * Copyright (c) 2004-2008 qadram software S.L.
 * Copyright (c) 2026 Gunnar Beushausen
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 */

declare(strict_types=1);

namespace VCL\Charts;

use VCL\UI\Control;

/**
 * Chart type constants.
 */
const CT_BAR = 'bar';
const CT_HORIZONTAL_BAR = 'horizontalBar';
const CT_LINE = 'line';
const CT_PIE = 'pie';
const CT_DOUGHNUT = 'doughnut';
const CT_RADAR = 'radar';
const CT_POLAR = 'polarArea';

/**
 * Modern chart component using Chart.js.
 *
 * Renders interactive charts using the Chart.js JavaScript library.
 *
 * Example usage:
 * ```php
 * $chart = new Chart($this);
 * $chart->Name = 'Chart1';
 * $chart->Parent = $this;
 * $chart->Width = 400;
 * $chart->Height = 300;
 * $chart->ChartType = CT_BAR;
 * $chart->Title = 'Sales by Quarter';
 * $chart->Labels = ['Q1', 'Q2', 'Q3', 'Q4'];
 * $chart->addDataset('2024', [65, 59, 80, 81], '#4e73df');
 * $chart->addDataset('2025', [75, 70, 90, 85], '#1cc88a');
 * ```
 */
class Chart extends Control
{
    protected string $_charttype = CT_BAR;
    protected string $_title = '';
    protected array $_labels = [];
    protected array $_datasets = [];
    protected bool $_legend = true;
    protected bool $_responsive = false;
    protected bool $_maintainaspectratio = true;
    protected string $_chartjsurl = 'https://cdn.jsdelivr.net/npm/chart.js';

    // =========================================================================
    // PROPERTY HOOKS
    // =========================================================================

    /**
     * The type of chart to render.
     */
    public string $ChartType {
        get => $this->_charttype;
        set => $this->_charttype = $value;
    }

    /**
     * The chart title.
     */
    public string $Title {
        get => $this->_title;
        set => $this->_title = $value;
    }

    /**
     * Labels for the X axis (or pie segments).
     */
    public array $Labels {
        get => $this->_labels;
        set => $this->_labels = $value;
    }

    /**
     * Show the legend.
     */
    public bool $Legend {
        get => $this->_legend;
        set => $this->_legend = $value;
    }

    /**
     * Make the chart responsive to container size.
     */
    public bool $Responsive {
        get => $this->_responsive;
        set => $this->_responsive = $value;
    }

    /**
     * Maintain aspect ratio when resizing.
     */
    public bool $MaintainAspectRatio {
        get => $this->_maintainaspectratio;
        set => $this->_maintainaspectratio = $value;
    }

    /**
     * URL to Chart.js library (CDN or local).
     */
    public string $ChartJsUrl {
        get => $this->_chartjsurl;
        set => $this->_chartjsurl = $value;
    }

    // =========================================================================
    // CONSTRUCTOR
    // =========================================================================

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->Width = 400;
        $this->Height = 300;
    }

    // =========================================================================
    // DATA METHODS
    // =========================================================================

    /**
     * Add a dataset to the chart.
     *
     * @param string $label Dataset label
     * @param array $data Data values
     * @param string $backgroundColor Background color (or array for pie/doughnut)
     * @param string $borderColor Border color
     */
    public function addDataset(
        string $label,
        array $data,
        string|array $backgroundColor = '#4e73df',
        string $borderColor = ''
    ): void {
        $dataset = [
            'label' => $label,
            'data' => $data,
            'backgroundColor' => $backgroundColor,
        ];

        if ($borderColor !== '') {
            $dataset['borderColor'] = $borderColor;
            $dataset['borderWidth'] = 1;
        }

        $this->_datasets[] = $dataset;
    }

    /**
     * Add a data point to the first dataset (simple API).
     *
     * @param string $label Label for this point
     * @param float|int $value The value
     * @param string $color Optional color for this point
     */
    public function addPoint(string $label, float|int $value, string $color = ''): void
    {
        $this->_labels[] = $label;

        if (empty($this->_datasets)) {
            $this->_datasets[] = [
                'label' => $this->_title,
                'data' => [],
                'backgroundColor' => [],
            ];
        }

        $this->_datasets[0]['data'][] = $value;

        if ($color !== '') {
            $this->_datasets[0]['backgroundColor'][] = $color;
        }
    }

    /**
     * Clear all data.
     */
    public function clearData(): void
    {
        $this->_labels = [];
        $this->_datasets = [];
    }

    // =========================================================================
    // RENDERING
    // =========================================================================

    public function dumpHeaderCode(): void
    {
        parent::dumpHeaderCode();

        // Include Chart.js only once
        if (!defined('VCL_CHARTJS_INCLUDED')) {
            define('VCL_CHARTJS_INCLUDED', 1);
            echo '<script src="' . htmlspecialchars($this->_chartjsurl) . '"></script>' . "\n";
        }
    }

    protected function dumpContents(): void
    {
        $style = 'width:' . $this->Width . 'px;height:' . $this->Height . 'px;';

        echo '<div style="' . $style . '">';
        echo '<canvas id="' . htmlspecialchars($this->Name) . '"></canvas>';
        echo '</div>';
    }

    /**
     * Render the chart as HTML string.
     */
    public function render(): string
    {
        ob_start();
        $this->dumpContents();
        return ob_get_clean();
    }

    public function dumpJavascript(): void
    {
        parent::dumpJavascript();

        $config = $this->buildChartConfig();
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        echo "document.addEventListener('DOMContentLoaded', function() {\n";
        echo "  var ctx = document.getElementById('" . $this->Name . "').getContext('2d');\n";
        echo "  window." . $this->Name . " = new Chart(ctx, " . $configJson . ");\n";
        echo "});\n";
    }

    // =========================================================================
    // PROTECTED METHODS
    // =========================================================================

    protected function buildChartConfig(): array
    {
        $config = [
            'type' => $this->_charttype,
            'data' => [
                'labels' => $this->_labels,
                'datasets' => $this->_datasets,
            ],
            'options' => [
                'responsive' => $this->_responsive,
                'maintainAspectRatio' => $this->_maintainaspectratio,
                'plugins' => [
                    'legend' => [
                        'display' => $this->_legend,
                    ],
                ],
            ],
        ];

        if ($this->_title !== '') {
            $config['options']['plugins']['title'] = [
                'display' => true,
                'text' => $this->_title,
            ];
        }

        return $config;
    }

    // =========================================================================
    // DEFAULT VALUE METHODS
    // =========================================================================

    protected function defaultChartType(): string
    {
        return CT_BAR;
    }

    protected function defaultTitle(): string
    {
        return '';
    }

    protected function defaultLabels(): array
    {
        return [];
    }

    protected function defaultLegend(): bool
    {
        return true;
    }

    protected function defaultResponsive(): bool
    {
        return false;
    }
}
