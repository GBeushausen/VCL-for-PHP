<?php

declare(strict_types=1);

namespace VCL\StdCtrls;

use VCL\UI\FocusControl;

/**
 * ComboBox is a dropdown selection control.
 *
 * PHP 8.4 version with Property Hooks.
 */
class ComboBox extends FocusControl
{
    protected array $_items = [];
    protected int $_itemindex = -1;
    protected string $_text = '';
    protected bool $_sorted = false;
    protected ?string $_onchange = null;

    // Property Hooks
    public array $Items {
        get => $this->_items;
        set {
            $this->_items = $value;
            if ($this->_sorted) {
                sort($this->_items);
            }
        }
    }

    public int $ItemIndex {
        get => $this->_itemindex;
        set => $this->_itemindex = $value;
    }

    public string $Text {
        get {
            if ($this->_itemindex >= 0 && isset($this->_items[$this->_itemindex])) {
                return $this->_items[$this->_itemindex];
            }
            return $this->_text;
        }
        set => $this->_text = $value;
    }

    public bool $Sorted {
        get => $this->_sorted;
        set {
            $this->_sorted = $value;
            if ($value) {
                sort($this->_items);
            }
        }
    }

    public ?string $OnChange {
        get => $this->_onchange;
        set => $this->_onchange = $value;
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->_width = 150;
        $this->_height = 25;
    }

    public function preinit(): void
    {
        parent::preinit();

        $submittedValue = $this->input->{$this->_name} ?? null;
        if (is_object($submittedValue)) {
            $value = $submittedValue->asString();
            // Find the index of the selected value
            $index = array_search($value, $this->_items);
            if ($index !== false) {
                $this->_itemindex = (int)$index;
            } else {
                $this->_text = $value;
            }
        }
    }

    protected function dumpContents(): void
    {
        $disabled = !$this->_enabled ? ' disabled' : '';
        $style = $this->buildComboStyle();

        $onchange = '';
        if ($this->_onchange !== null) {
            $onchange = " onchange=\"{$this->_onchange}(this)\"";
        }

        echo "<select id=\"{$this->_name}\" name=\"{$this->_name}\" style=\"{$style}\"{$disabled}{$onchange}>\n";

        foreach ($this->_items as $index => $item) {
            $selected = ($index === $this->_itemindex) ? ' selected' : '';
            echo "<option value=\"" . htmlspecialchars($item) . "\"{$selected}>" . htmlspecialchars($item) . "</option>\n";
        }

        echo "</select>\n";
    }

    protected function buildComboStyle(): string
    {
        $styles = [];
        $styles[] = "width: 100%";
        $styles[] = "height: 100%";
        $styles[] = "padding: 4px";
        $styles[] = "border: 1px solid #ccc";
        $styles[] = "border-radius: 3px";
        $styles[] = "font-family: inherit";
        $styles[] = "font-size: inherit";
        $styles[] = "background: white";
        $styles[] = "box-sizing: border-box";

        return implode('; ', $styles);
    }

    /**
     * Override render to use dumpContents.
     */
    public function render(): string
    {
        ob_start();
        $this->dumpContents();
        return ob_get_clean();
    }

    /**
     * Add an item to the list.
     */
    public function addItem(string $item): void
    {
        $this->_items[] = $item;
        if ($this->_sorted) {
            sort($this->_items);
        }
    }

    /**
     * Clear all items.
     */
    public function clear(): void
    {
        $this->_items = [];
        $this->_itemindex = -1;
    }

    // Legacy getters/setters
    public function getItems(): array { return $this->_items; }
    public function setItems(array $value): void { $this->Items = $value; }

    public function getItemIndex(): int { return $this->_itemindex; }
    public function setItemIndex(int $value): void { $this->ItemIndex = $value; }

    public function getText(): string { return $this->Text; }
    public function setText(string $value): void { $this->Text = $value; }

    public function getSorted(): bool { return $this->_sorted; }
    public function setSorted(bool $value): void { $this->Sorted = $value; }

    public function getOnChange(): ?string { return $this->_onchange; }
    public function setOnChange(?string $value): void { $this->OnChange = $value; }
}
