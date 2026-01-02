<?php

declare(strict_types=1);

namespace VCL\DBGrids;

use VCL\UI\CustomControl;

/**
 * CustomGrid is the base type for grid components.
 *
 * Describes the attributes of columns in a two-dimensional grid.
 *
 * PHP 8.4 version with Property Hooks.
 */
class CustomDBGrid extends CustomControl
{
    protected array $_columns = [];
    protected mixed $_datasource = null;
    protected string $_deletelink = '';

    // Property Hooks
    public array $Columns {
        get => $this->_columns;
        set => $this->_columns = $value;
    }

    public mixed $DataSource {
        get => $this->_datasource;
        set => $this->_datasource = $this->fixupProperty($value);
    }

    public string $DeleteLink {
        get => $this->_deletelink;
        set => $this->_deletelink = $value;
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->_width = 400;
        $this->_height = 200;
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
     * Add a column to the grid.
     */
    public function addColumn(array $column): void
    {
        $this->_columns[] = $column;
    }

    /**
     * Clear all columns.
     */
    public function clearColumns(): void
    {
        $this->_columns = [];
    }

    /**
     * Get a column by index.
     */
    public function getColumn(int $index): ?array
    {
        return $this->_columns[$index] ?? null;
    }

    /**
     * Get the number of columns.
     */
    public function getColumnCount(): int
    {
        return count($this->_columns);
    }

    // Legacy getters/setters
    public function readColumns(): array { return $this->_columns; }
    public function writeColumns(array $value): void { $this->Columns = $value; }
    public function defaultColumns(): array { return []; }

    public function getDeleteLink(): string { return $this->_deletelink; }
    public function setDeleteLink(string $value): void { $this->DeleteLink = $value; }
    public function defaultDeleteLink(): string { return ''; }

    public function getDataSource(): mixed { return $this->_datasource; }
    public function setDataSource(mixed $value): void { $this->DataSource = $value; }
}
