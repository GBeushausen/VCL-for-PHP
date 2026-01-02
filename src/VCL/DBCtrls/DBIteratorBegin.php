<?php

declare(strict_types=1);

namespace VCL\DBCtrls;

use VCL\UI\Control;

/**
 * DBIteratorBegin creates/begins a section on a templated form.
 *
 * Use this component to create a section on your templated form that will
 * iterate as many times as records found on the dataset attached.
 *
 * To end the section, use a DBIteratorEnd control and assign its IteratorBegin property.
 *
 * PHP 8.4 version with Property Hooks.
 */
class DBIteratorBegin extends Control
{
    protected mixed $_datasource = null;

    // Property Hooks
    public mixed $DataSource {
        get => $this->_datasource;
        set => $this->_datasource = $this->fixupProperty($value);
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
     * Dump the iterator begin marker.
     */
    public function dumpContents(): void
    {
        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            echo "<!-- DBIteratorBegin: {$this->_name} -->\n";
            return;
        }

        if ($this->_datasource === null || $this->_datasource->DataSet === null) {
            return;
        }

        $ds = $this->_datasource->DataSet;
        $ds->First();

        // Output iterator start marker for template processing
        echo "<!-- BEGIN_ITERATOR:{$this->_name} -->\n";
    }

    // Legacy getters/setters
    public function getDataSource(): mixed { return $this->_datasource; }
    public function setDataSource(mixed $value): void { $this->DataSource = $value; }
}
