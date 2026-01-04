<?php

declare(strict_types=1);

namespace VCL\DBCtrls;

use VCL\ExtCtrls\Panel;

// Repeater kind constants
if (!defined('rkVertical')) {
    define('rkVertical', 'rkVertical');
    define('rkHorizontal', 'rkHorizontal');
}

/**
 * DBRepeater repeats its child controls for each record in a dataset.
 *
 * This control iterates through a dataset and renders its children
 * for each record, either vertically or horizontally.
 *
 * PHP 8.4 version with Property Hooks.
 */
class DBRepeater extends Panel
{
    protected string $_kind = 'rkVertical';
    protected bool $_restartdataset = true;
    protected int $_limit = 0;
    protected mixed $_datasource = null;

    // Property Hooks
    public string $Kind {
        get => $this->_kind;
        set => $this->_kind = $value;
    }

    public bool $RestartDataset {
        get => $this->_restartdataset;
        set => $this->_restartdataset = $value;
    }

    public int $Limit {
        get => $this->_limit;
        set => $this->_limit = max(0, $value);
    }

    public mixed $DataSource {
        get => $this->_datasource;
        set => $this->_datasource = $this->fixupProperty($value);
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        if ($this->_layout !== null) {
            $this->_layout->Type = 'XY_LAYOUT';
        }
    }

    /**
     * Called when component is loaded.
     */
    public function loaded(): void
    {
        parent::loaded();
        $this->DataSource = $this->_datasource;
    }

    /**
     * Dump the repeater contents.
     */
    protected function dumpContents(): void
    {
        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            parent::dumpContents();
            return;
        }

        if ($this->_datasource === null || $this->_datasource->DataSet === null) {
            return;
        }

        $ds = $this->_datasource->DataSet;

        if ($this->_restartdataset) {
            $ds->First();
        }

        if ($ds->EOF()) {
            return;
        }

        $class = ($this->_style !== '') ? "class=\"{$this->_style}\"" : '';
        $isHorizontal = ($this->_kind === 'rkHorizontal');

        echo "<table id=\"{$this->_name}_table_detail\" width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" {$class}>\n";

        $render = 0;

        if ($isHorizontal) {
            echo "<tr>\n";
        }

        while (!$ds->EOF()) {
            if (!$isHorizontal) {
                echo "<tr>\n";
            }

            echo "<td>\n";
            parent::dumpContents();
            echo "</td>\n";

            if (!$isHorizontal) {
                echo "</tr>\n";
            }

            $ds->Next();
            $render++;

            if ($this->_limit !== 0 && $render >= $this->_limit) {
                break;
            }
        }

        if ($isHorizontal) {
            echo "</tr>\n";
        }

        echo "</table>\n";
    }

    // Legacy getters/setters
    public function getKind(): string { return $this->_kind; }
    public function setKind(string $value): void { $this->Kind = $value; }
    public function defaultKind(): string { return 'rkVertical'; }

    public function getRestartDataset(): bool { return $this->_restartdataset; }
    public function setRestartDataset(bool $value): void { $this->RestartDataset = $value; }
    public function defaultRestartDataset(): bool { return true; }

    public function getLimit(): int { return $this->_limit; }
    public function setLimit(int $value): void { $this->Limit = $value; }
    public function defaultLimit(): int { return 0; }

    public function getDataSource(): mixed { return $this->_datasource; }
    public function setDataSource(mixed $value): void { $this->DataSource = $value; }
}
