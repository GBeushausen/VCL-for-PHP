<?php

declare(strict_types=1);

namespace VCL\DBCtrls;

use VCL\UI\Control;

/**
 * DBIteratorEnd ends a section created with DBIteratorBegin.
 *
 * Use this component to end a section on your templated form that was
 * started with DBIteratorBegin.
 *
 * PHP 8.4 version with Property Hooks.
 */
class DBIteratorEnd extends Control
{
    protected mixed $_iteratorbegin = null;

    // Property Hooks
    public mixed $IteratorBegin {
        get => $this->_iteratorbegin;
        set => $this->_iteratorbegin = $this->fixupProperty($value);
    }

    /**
     * Called when component is loaded.
     */
    public function loaded(): void
    {
        parent::loaded();
        $this->IteratorBegin = $this->_iteratorbegin;
    }

    /**
     * Dump the iterator end marker.
     */
    protected function dumpContents(): void
    {
        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            echo "<!-- DBIteratorEnd: {$this->_name} -->\n";
            return;
        }

        $iteratorName = '';
        if ($this->_iteratorbegin !== null && is_object($this->_iteratorbegin)) {
            $iteratorName = $this->_iteratorbegin->Name ?? '';
        }

        // Output iterator end marker for template processing
        echo "<!-- END_ITERATOR:{$iteratorName} -->\n";
    }

    // Legacy getters/setters
    public function getIteratorBegin(): mixed { return $this->_iteratorbegin; }
    public function setIteratorBegin(mixed $value): void { $this->IteratorBegin = $value; }
}
