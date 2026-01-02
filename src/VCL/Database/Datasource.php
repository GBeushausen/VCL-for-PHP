<?php

declare(strict_types=1);

namespace VCL\Database;

use VCL\Core\Component;

/**
 * Datasource acts as a conduit between datasets and data-aware controls.
 *
 * A Datasource provides the interface between a dataset and data-aware
 * controls on a form. Data-aware controls must be associated with a
 * Datasource to display or edit data.
 *
 * PHP 8.4 version with Property Hooks.
 */
class Datasource extends Component
{
    protected mixed $_dataset = null;

    // Property Hooks
    public mixed $DataSet {
        get => $this->_dataset;
        set => $this->_dataset = $this->fixupProperty($value);
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
    }

    /**
     * Called when component is loaded.
     */
    public function loaded(): void
    {
        parent::loaded();
        $this->DataSet = $this->_dataset;
    }

    // Legacy getters/setters
    public function getDataSet(): mixed { return $this->_dataset; }
    public function setDataSet(mixed $value): void { $this->DataSet = $value; }
}
