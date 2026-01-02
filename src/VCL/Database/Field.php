<?php

declare(strict_types=1);

namespace VCL\Database;

use VCL\Core\VCLObject;

/**
 * Field encapsulates a database field name and display label.
 *
 * This class is used to encapsulate the name of a table field and the caption
 * to be shown on data-aware components.
 *
 * PHP 8.4 version with Property Hooks.
 */
class Field extends VCLObject
{
    private string $_fieldname = '';
    private string $_displaylabel = '';

    // Property Hooks
    public string $FieldName {
        get => $this->_fieldname;
        set => $this->_fieldname = $value;
    }

    public string $DisplayLabel {
        get => $this->_displaylabel;
        set => $this->_displaylabel = $value;
    }

    /**
     * Get the display name (DisplayLabel if set, otherwise FieldName).
     */
    public function getDisplayName(): string
    {
        return $this->_displaylabel !== '' ? $this->_displaylabel : $this->_fieldname;
    }

    // Legacy getters/setters
    public function getFieldName(): string { return $this->_fieldname; }
    public function setFieldName(string $value): void { $this->FieldName = $value; }

    public function getDisplayLabel(): string { return $this->_displaylabel; }
    public function setDisplayLabel(string $value): void { $this->DisplayLabel = $value; }
}
