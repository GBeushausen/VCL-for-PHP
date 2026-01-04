<?php
/**
 * VCL for PHP 3.0
 *
 * GridPanel - A responsive grid container using Tailwind CSS
 */

declare(strict_types=1);

namespace VCL\ExtCtrls;

use VCL\UI\Enums\RenderMode;

/**
 * GridPanel is a container that uses CSS Grid for layout.
 *
 * This component renders its children in a grid container with configurable
 * columns, gaps, and responsive breakpoints using Tailwind CSS classes.
 */
class GridPanel extends CustomPanel
{
    protected int $_columns = 1;
    protected array $_responsiveColumns = []; // ['sm' => 2, 'md' => 3, 'lg' => 4]
    protected string $_gridGap = 'gap-4';
    protected string $_rowGap = '';
    protected string $_colGap = '';
    protected string $_gridRows = '';
    protected bool $_autoFlow = false;
    protected string $_autoFlowDirection = 'row'; // 'row', 'col', 'dense', 'row-dense', 'col-dense'

    // Property Hooks
    public int $Columns {
        get => $this->_columns;
        set => $this->_columns = max(1, $value);
    }

    public array $ResponsiveColumns {
        get => $this->_responsiveColumns;
        set => $this->_responsiveColumns = $value;
    }

    public string $GridGap {
        get => $this->_gridGap;
        set => $this->_gridGap = $value;
    }

    public string $RowGap {
        get => $this->_rowGap;
        set => $this->_rowGap = $value;
    }

    public string $ColGap {
        get => $this->_colGap;
        set => $this->_colGap = $value;
    }

    public string $GridRows {
        get => $this->_gridRows;
        set => $this->_gridRows = $value;
    }

    public bool $AutoFlow {
        get => $this->_autoFlow;
        set => $this->_autoFlow = $value;
    }

    public string $AutoFlowDirection {
        get => $this->_autoFlowDirection;
        set => $this->_autoFlowDirection = $value;
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        // GridPanel always uses Tailwind mode
        $this->_renderMode = RenderMode::Tailwind;
    }

    protected function getComponentType(): string
    {
        return 'grid';
    }

    /**
     * Build the grid container classes.
     */
    protected function getGridClasses(): string
    {
        $classes = ['grid'];

        // Columns
        $classes[] = "grid-cols-{$this->_columns}";

        // Responsive columns
        foreach ($this->_responsiveColumns as $breakpoint => $cols) {
            $cols = (int)$cols;
            if ($cols > 0) {
                $classes[] = "{$breakpoint}:grid-cols-{$cols}";
            }
        }

        // Rows (if specified)
        if ($this->_gridRows !== '') {
            $classes[] = $this->_gridRows;
        }

        // Gap
        if ($this->_gridGap !== '') {
            $classes[] = $this->_gridGap;
        }

        // Specific row/col gaps override general gap
        if ($this->_rowGap !== '') {
            $classes[] = $this->_rowGap;
        }
        if ($this->_colGap !== '') {
            $classes[] = $this->_colGap;
        }

        // Auto flow
        if ($this->_autoFlow) {
            $flowClass = match ($this->_autoFlowDirection) {
                'row' => 'grid-flow-row',
                'col' => 'grid-flow-col',
                'dense' => 'grid-flow-dense',
                'row-dense' => 'grid-flow-row-dense',
                'col-dense' => 'grid-flow-col-dense',
                default => 'grid-flow-row',
            };
            $classes[] = $flowClass;
        }

        return implode(' ', $classes);
    }

    /**
     * Render the grid panel.
     */
    public function dumpContents(): void
    {
        $name = htmlspecialchars($this->Name);

        // Build class list
        $classes = [];
        $classes[] = $this->getGridClasses();

        // Add padding/margin from Control
        if ($this->_padding !== '') {
            $classes[] = $this->_padding;
        }
        if ($this->_margin !== '') {
            $classes[] = $this->_margin;
        }

        // Add custom Tailwind classes
        if (!empty($this->_cssClasses)) {
            $classes = array_merge($classes, $this->_cssClasses);
        }

        // Add Style property class
        $styleClass = $this->readStyleClass();
        if ($styleClass !== '') {
            $classes[] = $styleClass;
        }

        $classAttr = 'class="' . htmlspecialchars(implode(' ', array_filter($classes))) . '"';

        // Build inline style (minimal for Tailwind mode)
        $style = $this->getInlineStyle();
        $styleAttr = $style !== '' ? " style=\"{$style}\"" : '';

        echo "<div id=\"{$name}\" {$classAttr}{$styleAttr}>\n";

        // Render child controls
        if ($this->controls !== null) {
            foreach ($this->controls->items as $child) {
                if (!$child->Visible) {
                    continue;
                }

                // Grid children are rendered directly (grid handles placement)
                if (method_exists($child, 'show')) {
                    $child->show();
                } elseif (method_exists($child, 'dumpContents')) {
                    $child->dumpContents();
                } else {
                    echo $child->render();
                }
            }
        }

        echo "</div>\n";
    }

    // Legacy getters/setters
    public function getColumns(): int { return $this->_columns; }
    public function setColumns(int $value): void { $this->Columns = $value; }

    public function getResponsiveColumns(): array { return $this->_responsiveColumns; }
    public function setResponsiveColumns(array $value): void { $this->ResponsiveColumns = $value; }

    public function getGridGap(): string { return $this->_gridGap; }
    public function setGridGap(string $value): void { $this->GridGap = $value; }

    public function getRowGap(): string { return $this->_rowGap; }
    public function setRowGap(string $value): void { $this->RowGap = $value; }

    public function getColGap(): string { return $this->_colGap; }
    public function setColGap(string $value): void { $this->ColGap = $value; }

    public function getGridRows(): string { return $this->_gridRows; }
    public function setGridRows(string $value): void { $this->GridRows = $value; }

    public function getAutoFlow(): bool { return $this->_autoFlow; }
    public function setAutoFlow(bool $value): void { $this->AutoFlow = $value; }

    public function getAutoFlowDirection(): string { return $this->_autoFlowDirection; }
    public function setAutoFlowDirection(string $value): void { $this->AutoFlowDirection = $value; }
}
