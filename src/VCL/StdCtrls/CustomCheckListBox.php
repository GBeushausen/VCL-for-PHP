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

namespace VCL\StdCtrls;

use VCL\UI\FocusControl;

/**
 * Base class for CheckListBox.
 *
 * A control for generating a list where all items have a checkbox at the left.
 * Users can check or uncheck items in the list.
 */
class CustomCheckListBox extends FocusControl
{
    protected array $_items = [];
    protected array $_checked = [];
    protected array $_header = [];

    protected int $_borderstyle = BS_SINGLE;
    protected int $_borderwidth = 1;
    protected string $_bordercolor = '#CCCCCC';

    protected ?string $_onclick = null;
    protected ?string $_onsubmit = null;

    protected int $_taborder = 0;
    protected int $_tabstop = 1;
    protected int $_columns = 1;

    protected string $_headerbackgroundcolor = '#CCCCCC';
    protected string $_headercolor = '#FFFFFF';

    // =========================================================================
    // PROPERTY HOOKS
    // =========================================================================

    public array $Items {
        get => $this->_items;
        set => $this->_items = $value;
    }

    public array $Checked {
        get => $this->_checked;
        set {
            $this->_checked = [];
            foreach ($value as $k => $v) {
                $this->_checked[$k] = $v;
            }
        }
    }

    public array $Header {
        get => $this->_header;
        set {
            $this->_header = [];
            foreach ($value as $k => $v) {
                $this->_header[$k] = $v;
            }
        }
    }

    public int $BorderStyle {
        get => $this->_borderstyle;
        set => $this->_borderstyle = $value;
    }

    public int $BorderWidth {
        get => $this->_borderwidth;
        set => $this->_borderwidth = $value;
    }

    public string $BorderColor {
        get => $this->_bordercolor;
        set => $this->_bordercolor = $value;
    }

    public ?string $OnClick {
        get => $this->_onclick;
        set => $this->_onclick = $value;
    }

    public ?string $OnSubmit {
        get => $this->_onsubmit;
        set => $this->_onsubmit = $value;
    }

    public int $TabOrder {
        get => $this->_taborder;
        set => $this->_taborder = $value;
    }

    public int $TabStop {
        get => $this->_tabstop;
        set => $this->_tabstop = $value;
    }

    public int $Columns {
        get => $this->_columns;
        set => $this->_columns = $value ?: 1;
    }

    public string $HeaderBackgroundColor {
        get => $this->_headerbackgroundcolor;
        set => $this->_headerbackgroundcolor = $value;
    }

    public string $HeaderColor {
        get => $this->_headercolor;
        set => $this->_headercolor = $value;
    }

    public int $Count {
        get => count($this->_items);
    }

    // =========================================================================
    // CONSTRUCTOR
    // =========================================================================

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->clear();

        $this->Width = 185;
        $this->Height = 89;
    }

    // =========================================================================
    // LIFECYCLE METHODS
    // =========================================================================

    public function preinit(): void
    {
        parent::preinit();

        $submitted = $this->input->{$this->Name} ?? null;

        if (is_object($submitted)) {
            $this->_checked = $submitted->asStringArray();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // There is a post, but nothing for this control posted, so clean the array
            $this->_checked = [];
        }
    }

    public function init(): void
    {
        parent::init();

        $submitted = $this->input->{$this->Name} ?? null;

        if (is_object($submitted)) {
            if ($this->_onsubmit !== null) {
                $this->callEvent('onsubmit', []);
            }
        }

        $submitEvent = $this->input->{$this->readJSWrapperHiddenFieldName()} ?? null;

        if (is_object($submitEvent) && $this->_enabled) {
            if ($this->_onclick !== null && $submitEvent->asString() === $this->readJSWrapperSubmitEventValue($this->_onclick)) {
                $this->callEvent('onclick', []);
            }
        }
    }

    // =========================================================================
    // RENDERING
    // =========================================================================

    public function dumpContents(): void
    {
        $events = '';
        if ($this->_enabled) {
            $events = $this->readJsEvents();
            $this->addJSWrapperToEvents($events, $this->_onclick, $this->_jsonclick ?? null, 'onclick');
        }

        // Build border style
        $border = '';
        if ($this->_borderstyle !== BS_NONE) {
            $border = 'solid';
            if ($this->_borderwidth !== 0) {
                $border .= ' ' . $this->_borderwidth . 'px';
            }
            if ($this->_bordercolor !== '') {
                $border .= ' ' . $this->_bordercolor;
            }
            $border = 'border: ' . $border . ';';
        }

        $class = $this->Style !== '' ? 'class="' . $this->StyleClass . '"' : '';

        echo '<div style="overflow-y:auto; overflow-x:hidden; width:' . $this->Width . 'px; height:' . $this->Height . 'px; ' . $border . '" ' . $class . '>' . "\n";

        $style = '';
        if ($this->Style === '') {
            $style .= $this->Font->FontString;

            if ($this->Color !== '') {
                $style .= 'background-color: ' . $this->Color . ';';
            }

            if ($this->_cursor !== '') {
                $cr = strtolower(substr($this->_cursor, 2));
                $style .= 'cursor: ' . $cr . ';';
            }
        }

        $spanstyle = $style;

        // Set enabled/disabled status
        $enabled = !$this->_enabled ? 'disabled="disabled"' : '';

        // Set tab order if tab stop set to true
        $taborder = $this->_tabstop ? 'tabindex="' . $this->_taborder . '"' : '';

        // Add correct layout table for the grouping
        $style .= 'table-layout:fixed;';

        // Get the hint attribute
        $hint = $this->getHintAttribute();

        if ($style !== '') {
            $style = 'style="' . $style . '"';
        }
        if ($spanstyle !== '') {
            $spanstyle = 'style="' . $spanstyle . '"';
        }

        // Get the alignment of the items
        $alignment = '';

        // Call the OnShow event if assigned
        if ($this->_onshow !== null) {
            $this->callEvent('onshow', []);
        }

        $hinttext = ($this->_hint !== '' && $this->ShowHint) ? $this->_hint : '';

        echo '<table cellpadding="0" cellspacing="0" title="' . htmlspecialchars($hinttext) . '" ' . $style . ' ' . $class . '>';

        if (count($this->_items) > 0) {
            $w = $this->_width;
            $h = $this->_height;
            $index = 0;

            // Calculate layout
            $numItems = count($this->_items);
            $columnsWidth = $w / $this->_columns;
            $itemsPerColumn = (int)ceil($numItems / $this->_columns);
            $rowHeight = $h / $itemsPerColumn;
            $itemsPerRow = (int)ceil($numItems / $itemsPerColumn);

            for ($row = 0; $row < $itemsPerColumn; ++$row) {
                echo "<tr>\n";

                for ($column = 0; $column < $itemsPerRow; ++$column) {
                    $curItemNum = $row + $itemsPerColumn * $column;
                    if ($curItemNum < $numItems) {
                        $key = $curItemNum;
                        $item = $this->_items[$key];
                        $element = $this->Name . '_' . $key;
                        $itemWidth = $columnsWidth - 20;

                        $headerStyle = '';
                        if (isset($this->_header[$curItemNum]) && $this->_header[$curItemNum]) {
                            echo '<td width="20" style="color:' . $this->_headercolor . '; background-color:' . $this->_headerbackgroundcolor . '"><input id="' . $element . '" type="checkbox" style="visibility:hidden"></td>' . "\n";
                            $headerStyle = ' color:' . $this->_headercolor . '; background-color:' . $this->_headerbackgroundcolor . ' ';
                        } else {
                            $checked = (isset($this->_checked[$key]) && $this->_checked[$key]) ? 'checked="checked"' : '';
                            echo '<td width="20"><input id="' . $element . '" type="checkbox" name="' . $this->Name . '[' . $key . ']" value="1" ' . $events . ' ' . $enabled . ' ' . $taborder . ' ' . $hint . ' ' . $class . ' ' . $checked . '></td>' . "\n";
                        }

                        $itemclick = ($this->_enabled && $this->Owner !== null)
                            ? 'onclick="return CheckListBoxClick(\'' . $this->Name . '_' . $index . '\', ' . $index . ');"'
                            : '';

                        echo '</td><td ' . $alignment . ' width="' . $itemWidth . '" height="' . $rowHeight . '" style="overflow:hidden;white-space:nowrap; ' . $headerStyle . '">' . "\n";
                        echo '<span id="' . $element . '" style="white-space:nowrap;" ' . $itemclick . ' ' . $spanstyle . ' ' . $class . '>' . htmlspecialchars((string)$item) . '</span>' . "\n";
                    }
                }
                echo "</tr>\n";
            }
        }

        echo "</table>\n";
        echo '</div>';

        // Add a hidden field to determine which control fired the event
        if ($this->_onclick !== null) {
            echo "\n";
            echo '<input type="hidden" name="' . $this->readJSWrapperHiddenFieldName() . '" value="">';
        }
    }

    public function dumpFormItems(): void
    {
        if ($this->_onclick !== null) {
            $hiddenwrapperfield = $this->readJSWrapperHiddenFieldName();
            echo '<input type="hidden" id="' . $hiddenwrapperfield . '" name="' . $hiddenwrapperfield . '" value="">';
        }
    }

    public function dumpJavascript(): void
    {
        parent::dumpJavascript();

        if ($this->_enabled) {
            if ($this->_onclick !== null && !defined($this->_onclick)) {
                $def = $this->_onclick;
                define($def, 1);
                echo $this->getJSWrapperFunction($this->_onclick);
            }

            // Output the CheckListBoxClick function only once
            if (!defined('CheckListBoxClick')) {
                define('CheckListBoxClick', 1);

                echo "function CheckListBoxClick(name, index)\n";
                echo "{\n";
                echo "  var obj=document.getElementById(name);\n";
                echo "  if (obj) {\n";
                echo "    if (!obj.disabled) {\n";
                echo "      obj.checked = !obj.checked;\n";
                echo "      if (obj.onclick) return obj.onclick();\n";
                echo "    }\n";
                echo "  }\n";
                echo "  return false;\n";
                echo "}\n";
            }
        }
    }

    // =========================================================================
    // PUBLIC METHODS
    // =========================================================================

    /**
     * Adds an item to the list and returns the number of items on the list.
     */
    public function addItem(mixed $item, mixed $itemkey = null): int
    {
        end($this->_items);
        if ($itemkey !== null) {
            $this->_items[$itemkey] = $item;
        } else {
            $this->_items[] = $item;
        }
        return $this->Count;
    }

    /**
     * Deletes all items from the list.
     */
    public function clear(): void
    {
        $this->_items = [];
        $this->_checked = [];
    }

    /**
     * Return the item in the list box specified by $itemkey.
     */
    public function itemAtPos(mixed $itemkey): mixed
    {
        return $this->_items[$itemkey] ?? null;
    }

    /**
     * Set the checked property for all items to true.
     */
    public function selectAll(): void
    {
        foreach ($this->_items as $k => $v) {
            $this->_checked[$k] = true;
        }
    }

    /**
     * Set the checked property for all items to false.
     */
    public function deselectAll(): void
    {
        $this->_checked = [];
    }

    /**
     * Check if a specific item is checked.
     */
    public function isItemChecked(int $index): bool
    {
        return isset($this->_checked[$index]) && $this->_checked[$index];
    }

    /**
     * Set the checked state of a specific item.
     */
    public function setItemChecked(int $index, bool $checked): void
    {
        $this->_checked[$index] = $checked;
    }

    // =========================================================================
    // DEFAULT VALUE METHODS (for serialization)
    // =========================================================================

    protected function defaultBorderStyle(): int
    {
        return BS_SINGLE;
    }

    protected function defaultBorderWidth(): int
    {
        return 1;
    }

    protected function defaultBorderColor(): string
    {
        return '#CCCCCC';
    }

    protected function defaultItems(): array
    {
        return [];
    }

    protected function defaultChecked(): array
    {
        return [];
    }

    protected function defaultHeader(): array
    {
        return [];
    }

    protected function defaultTabOrder(): int
    {
        return 0;
    }

    protected function defaultTabStop(): int
    {
        return 1;
    }

    protected function defaultColumns(): int
    {
        return 1;
    }

    protected function defaultHeaderBackgroundColor(): string
    {
        return '#CCCCCC';
    }

    protected function defaultHeaderColor(): string
    {
        return '#FFFFFF';
    }
}
