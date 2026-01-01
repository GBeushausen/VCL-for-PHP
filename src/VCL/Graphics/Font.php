<?php

declare(strict_types=1);

namespace VCL\Graphics;

use VCL\Core\Persistent;
use VCL\Graphics\Enums\TextAlign;
use VCL\Graphics\Enums\FontStyle;
use VCL\Graphics\Enums\TextCase;
use VCL\Graphics\Enums\FontVariant;

/**
 * Font encapsulates all properties required to represent a font on the browser.
 *
 * Font describes font characteristics used when displaying text. Font defines a set
 * of characters by specifying the height, font name (typeface), attributes (such as bold or
 * italic) and so on.
 */
class Font extends Persistent
{
    private string $_family = 'Verdana';
    private string $_size = '10px';
    private string $_color = '';
    private string $_weight = '';
    private TextAlign $_align = TextAlign::None;
    private FontStyle $_style = FontStyle::Normal;
    private TextCase $_case = TextCase::None;
    private FontVariant $_variant = FontVariant::Normal;
    private string $_lineHeight = '';

    public ?object $_control = null;
    private int $_updateCounter = 0;

    // Property Hooks
    public string $Family {
        get => $this->_family;
        set {
            $this->_family = $value;
            $this->modified();
        }
    }

    public string $Size {
        get => $this->_size;
        set {
            $this->_size = $value;
            $this->modified();
        }
    }

    public string $Color {
        get => $this->_color;
        set {
            $this->_color = $value;
            $this->modified();
        }
    }

    public string $Weight {
        get => $this->_weight;
        set {
            $this->_weight = $value;
            $this->modified();
        }
    }

    public TextAlign|string $Align {
        get => $this->_align;
        set {
            $this->_align = $value instanceof TextAlign ? $value : TextAlign::from($value);
            $this->modified();
        }
    }

    public FontStyle|string $Style {
        get => $this->_style;
        set {
            $this->_style = $value instanceof FontStyle ? $value : FontStyle::from($value);
            $this->modified();
        }
    }

    public TextCase|string $Case {
        get => $this->_case;
        set {
            $this->_case = $value instanceof TextCase ? $value : TextCase::from($value);
            $this->modified();
        }
    }

    public FontVariant|string $Variant {
        get => $this->_variant;
        set {
            $this->_variant = $value instanceof FontVariant ? $value : FontVariant::from($value);
            $this->modified();
        }
    }

    public string $LineHeight {
        get => $this->_lineHeight;
        set {
            $this->_lineHeight = $value;
            $this->modified();
        }
    }

    /**
     * Get owner of this font.
     */
    public function readOwner(): mixed
    {
        return $this->_control;
    }

    /**
     * Start batch update mode.
     */
    public function startUpdate(): void
    {
        $this->_updateCounter++;
    }

    /**
     * End batch update mode.
     */
    public function endUpdate(): void
    {
        $this->_updateCounter--;
        if ($this->_updateCounter < 0) {
            $this->_updateCounter = 0;
        }
        if ($this->_updateCounter === 0) {
            $this->modified();
        }
    }

    /**
     * Check if in update mode.
     */
    public function isUpdating(): bool
    {
        return $this->_updateCounter !== 0;
    }

    /**
     * Called when font is modified.
     */
    protected function modified(): void
    {
        if ($this->isUpdating() || $this->_control === null) {
            return;
        }

        if (($this->_control->_controlstate ?? 0) & CS_LOADING) {
            return;
        }

        if (($this->_control->_name ?? '') === '') {
            return;
        }

        // Check for ParentFont reset
        if (property_exists($this->_control, 'ParentFont') &&
            property_exists($this->_control, 'DoParentReset') &&
            $this->_control->ParentFont &&
            $this->_control->DoParentReset) {
            $this->_control->ParentFont = false;
        }

        if (method_exists($this->_control, 'updateChildrenFonts')) {
            $this->_control->updateChildrenFonts();
        }
    }

    /**
     * Assign font properties to another font.
     */
    public function assignTo(Persistent $dest): void
    {
        if (!($dest instanceof Font)) {
            $dest->assignError($this);
            return;
        }

        $dest->startUpdate();
        $dest->Family = $this->_family;
        $dest->Size = $this->_size;
        $dest->Color = $this->_color;
        $dest->Weight = $this->_weight;
        $dest->Align = $this->_align;
        $dest->Style = $this->_style;
        $dest->Case = $this->_case;
        $dest->Variant = $this->_variant;
        $dest->LineHeight = $this->_lineHeight;
        $dest->endUpdate();
    }

    /**
     * Generate CSS font string.
     */
    public function readFontString(): string
    {
        $styles = [];

        $styles[] = "font-family: {$this->_family}";
        $styles[] = "font-size: {$this->_size}";

        if ($this->_color !== '') {
            $styles[] = "color: {$this->_color}";
        }

        if ($this->_weight !== '') {
            $styles[] = "font-weight: {$this->_weight}";
        }

        if ($this->_lineHeight !== '') {
            $styles[] = "line-height: {$this->_lineHeight}";
        }

        if ($this->_align !== TextAlign::None) {
            $styles[] = rtrim($this->_align->toCss(), ';');
        }

        if ($this->_style !== FontStyle::Normal) {
            $styles[] = rtrim($this->_style->toCss(), ';');
        }

        if ($this->_case !== TextCase::None) {
            $styles[] = rtrim($this->_case->toCss(), ';');
        }

        if ($this->_variant !== FontVariant::Normal) {
            $styles[] = rtrim($this->_variant->toCss(), ';');
        }

        return implode('; ', $styles) . ';';
    }

    // Legacy getters/setters
    public function getFamily(): string { return $this->_family; }
    public function setFamily(string $value): void { $this->Family = $value; }
    public function defaultFamily(): string { return 'Verdana'; }

    public function getSize(): string { return $this->_size; }
    public function setSize(string $value): void { $this->Size = $value; }
    public function defaultSize(): string { return '10px'; }

    public function getColor(): string { return $this->_color; }
    public function setColor(string $value): void { $this->Color = $value; }
    public function defaultColor(): string { return ''; }

    public function getWeight(): string { return $this->_weight; }
    public function setWeight(string $value): void { $this->Weight = $value; }
    public function defaultWeight(): string { return ''; }

    public function getAlign(): TextAlign|string { return $this->_align; }
    public function setAlign(TextAlign|string $value): void { $this->Align = $value; }
    public function defaultAlign(): string { return 'taNone'; }

    public function getStyle(): FontStyle|string { return $this->_style; }
    public function setStyle(FontStyle|string $value): void { $this->Style = $value; }
    public function defaultStyle(): string { return ''; }

    public function getCase(): TextCase|string { return $this->_case; }
    public function setCase(TextCase|string $value): void { $this->Case = $value; }
    public function defaultCase(): string { return ''; }

    public function getVariant(): FontVariant|string { return $this->_variant; }
    public function setVariant(FontVariant|string $value): void { $this->Variant = $value; }
    public function defaultVariant(): string { return ''; }

    public function getLineHeight(): string { return $this->_lineHeight; }
    public function setLineHeight(string $value): void { $this->LineHeight = $value; }
    public function defaultLineHeight(): string { return ''; }
}
