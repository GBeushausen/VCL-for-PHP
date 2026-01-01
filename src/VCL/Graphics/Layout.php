<?php

declare(strict_types=1);

namespace VCL\Graphics;

use VCL\Core\Persistent;
use VCL\Graphics\Enums\LayoutType;

/**
 * Layout encapsulation to allow any component to hold controls and render them in different ways.
 *
 * Layout provides various layout modes:
 * - FLOW_LAYOUT: Controls rendered without any layout, one after another
 * - XY_LAYOUT: Controls rendered in fixed positions using HTML tables
 * - ABS_XY_LAYOUT: Controls rendered using absolute positioning
 * - REL_XY_LAYOUT: Controls rendered using relative positions
 * - GRIDBAG_LAYOUT: Controls rendered in a grid
 * - ROW_LAYOUT: Controls rendered in a single row
 * - COL_LAYOUT: Controls rendered in a single column
 */
class Layout extends Persistent
{
    public ?object $_control = null;

    private LayoutType $_type = LayoutType::AbsXY;
    private int $_rows = 5;
    private int $_cols = 5;
    private bool $_usePixelTrans = true;

    // Property Hooks
    public LayoutType|string $Type {
        get => $this->_type;
        set => $this->_type = $value instanceof LayoutType ? $value : LayoutType::from($value);
    }

    public int $Rows {
        get => $this->_rows;
        set => $this->_rows = max(1, $value);
    }

    public int $Cols {
        get => $this->_cols;
        set => $this->_cols = max(1, $value);
    }

    public bool $UsePixelTrans {
        get => $this->_usePixelTrans;
        set => $this->_usePixelTrans = $value;
    }

    public function readOwner(): mixed
    {
        return $this->_control;
    }

    /**
     * Dump layout contents based on type.
     */
    public function dumpLayoutContents(array $exclude = []): void
    {
        match($this->_type) {
            LayoutType::Col => $this->dumpColLayout($exclude),
            LayoutType::Row => $this->dumpRowLayout($exclude),
            LayoutType::GridBag => $this->dumpGridBagLayout($exclude),
            LayoutType::AbsXY => $this->dumpABSLayout($exclude),
            LayoutType::RelXY => $this->dumpRELLayout($exclude),
            LayoutType::XY => $this->dumpXYLayout($exclude),
            LayoutType::Flow => $this->dumpFlowLayout($exclude),
        };
    }

    /**
     * Dump absolute positioned layout.
     */
    public function dumpABSLayout(array $exclude = []): void
    {
        if ($this->_control === null || !property_exists($this->_control, 'controls')) {
            return;
        }

        foreach ($this->_control->controls->items as $k => $v) {
            if (!empty($exclude) && in_array($v->className(), $exclude, true)) {
                continue;
            }

            if (!$this->shouldDumpControl($v)) {
                continue;
            }

            $style = sprintf(
                "Z-INDEX: %d; LEFT: %dpx; WIDTH: %dpx; POSITION: absolute; TOP: %dpx; HEIGHT: %dpx",
                $k, $v->Left, $v->Width, $v->Top, $v->Height
            );

            echo "<div id=\"{$v->_name}_outer\" style=\"{$style}\">\n";
            $v->show();
            echo "\n</div>\n";
        }
    }

    /**
     * Dump relative positioned layout.
     */
    public function dumpRELLayout(array $exclude = []): void
    {
        if ($this->_control === null || !property_exists($this->_control, 'controls')) {
            return;
        }

        $controls = $this->_control->controls->items;
        usort($controls, fn($a, $b) => $a->Top <=> $b->Top);

        $shift = 0;
        foreach ($controls as $k => $v) {
            if (!empty($exclude) && in_array($v->className(), $exclude, true)) {
                continue;
            }

            if (!$this->shouldDumpControl($v)) {
                continue;
            }

            $top = $v->Top - $shift;
            $shift += $v->Height;

            $style = sprintf(
                "Z-INDEX: %d; LEFT: %dpx; WIDTH: %dpx; POSITION: relative; TOP: %dpx; HEIGHT: %dpx",
                $k, $v->Left, $v->Width, $top, $v->Height
            );

            echo "<div id=\"{$v->_name}_outer\" style=\"{$style}\">\n";
            $v->show();
            echo "\n</div>\n";
        }
    }

    /**
     * Dump flow layout.
     */
    public function dumpFlowLayout(array $exclude = []): void
    {
        if ($this->_control === null || !property_exists($this->_control, 'controls')) {
            return;
        }

        foreach ($this->_control->controls->items as $v) {
            if (!empty($exclude) && in_array($v->className(), $exclude, true)) {
                continue;
            }

            if (!$this->shouldDumpControl($v)) {
                continue;
            }

            echo "<span id=\"{$v->Name}_outer\">\n";
            $v->show();
            echo "\n</span>\n";
        }
    }

    /**
     * Dump grid bag layout.
     */
    public function dumpGridBagLayout(array $exclude = []): void
    {
        $this->dumpGrid($this->_cols, $this->_rows, '100%', $exclude);
    }

    /**
     * Dump row layout.
     */
    public function dumpRowLayout(array $exclude = []): void
    {
        $this->dumpGrid($this->_cols, 1, '100%', $exclude);
    }

    /**
     * Dump column layout.
     */
    public function dumpColLayout(array $exclude = []): void
    {
        $this->dumpGrid(1, $this->_rows, '100%', $exclude);
    }

    /**
     * Dump XY layout using tables.
     */
    public function dumpXYLayout(array $exclude = []): void
    {
        // Simplified implementation - use ABS layout for modern browsers
        $this->dumpABSLayout($exclude);
    }

    /**
     * Dump grid layout.
     */
    public function dumpGrid(int $cols, int $rows, string $width, array $exclude = []): void
    {
        if ($this->_control === null || !property_exists($this->_control, 'controls')) {
            return;
        }

        $pwidth = $this->_control->Width ?? 100;
        $pheight = $this->_control->Height ?? 100;

        $cwidth = (int)round($pwidth / $cols);
        $cheight = (int)round($pheight / $rows);

        $controls = [];
        foreach ($this->_control->controls->items as $v) {
            $col = (int)round($v->Left / $cwidth);
            $row = (int)round($v->Top / $cheight);
            $controls[$col][$row] = $v;
        }

        echo "<table width=\"{$width}\" height=\"{$pheight}\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";

        for ($y = 0; $y < $rows; $y++) {
            echo "<tr>\n";
            for ($x = 0; $x < $cols; $x++) {
                if (isset($controls[$x][$y]) && is_object($controls[$x][$y])) {
                    $v = $controls[$x][$y];
                    if (property_exists($v, 'AdjustToLayout')) {
                        $v->AdjustToLayout = true;
                    }

                    $pw = (int)round((100 * $v->Width) / $pwidth);
                    $ph = (int)round((100 * $v->Height) / $pheight);

                    echo "<td valign=\"top\" width=\"{$pw}%\" height=\"{$ph}%\">\n";
                    echo "<div id=\"{$v->Name}_outer\" style=\"height:100%;width:100%;\">\n";
                    $v->show();
                    echo "\n</div>\n";
                    echo "</td>\n";
                } else {
                    echo "<td>&nbsp;</td>\n";
                }
            }
            echo "</tr>\n";
        }
        echo "</table>\n";
    }

    /**
     * Check if control should be dumped.
     */
    protected function shouldDumpControl(object $control): bool
    {
        if (!property_exists($control, 'Visible') || !$control->Visible) {
            return false;
        }

        if (property_exists($control, 'IsLayer') && $control->IsLayer) {
            return false;
        }

        if ($this->_control !== null && method_exists($this->_control, 'getActiveLayer')) {
            if (property_exists($control, 'Layer')) {
                return (string)$control->Layer === (string)$this->_control->ActiveLayer;
            }
        }

        return true;
    }

    // Legacy getters/setters
    public function getType(): LayoutType|string { return $this->_type; }
    public function setType(LayoutType|string $value): void { $this->Type = $value; }
    public function defaultType(): string { return 'ABS_XY_LAYOUT'; }

    public function getRows(): int { return $this->_rows; }
    public function setRows(int $value): void { $this->Rows = $value; }
    public function defaultRows(): int { return 5; }

    public function getCols(): int { return $this->_cols; }
    public function setCols(int $value): void { $this->Cols = $value; }
    public function defaultCols(): int { return 5; }

    public function getUsePixelTrans(): bool { return $this->_usePixelTrans; }
    public function setUsePixelTrans(bool $value): void { $this->UsePixelTrans = $value; }
    public function defaultUsePixelTrans(): int { return 1; }
}
