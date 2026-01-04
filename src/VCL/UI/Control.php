<?php

declare(strict_types=1);

namespace VCL\UI;

use VCL\Core\Component;
use VCL\Core\Collection;
use VCL\Graphics\Font;
use VCL\UI\Enums\Alignment;
use VCL\UI\Enums\AlignItems;
use VCL\UI\Enums\Anchors;
use VCL\UI\Enums\Cursor;
use VCL\UI\Enums\RenderMode;
use VCL\UI\Enums\ResponsiveWidth;

/**
 * Control is the base class for all visual components.
 *
 * Controls are components that are visible at runtime. They have properties
 * for position, size, appearance, and user interaction through events.
 *
 * PHP 8.4 version with Property Hooks for clean getter/setter syntax.
 */
class Control extends Component
{
    // Position and size backing fields
    protected int $_left = 0;
    protected int $_top = 0;
    protected ?int $_width = null;
    protected ?int $_height = null;

    // Appearance backing fields
    protected string $_caption = '';
    protected string $_color = '';
    protected string $_hint = '';
    protected bool $_visible = true;
    protected bool $_enabled = true;
    protected bool $_showHint = false;
    protected string $_style = '';
    protected string $_designcolor = '';
    protected bool $_hidden = false;
    protected bool $_divwrap = true;
    protected bool $_autosize = false;
    protected string $_adjusttolayout = '0';
    protected array $_attributes = [];
    protected array $_controlstyle = [];

    // Parent/alignment backing fields
    protected ?Control $_parent = null;
    protected Alignment|string $_align = Alignment::None;
    protected Anchors|string $_alignment = Anchors::None;
    protected Cursor|string $_cursor = Cursor::Default;

    // Font (lazy loaded)
    protected ?Font $_font = null;
    protected bool $_parentFont = true;
    protected bool $_parentColor = true;
    protected bool $_parentShowHint = true;
    protected bool $_doParentReset = true;

    // Layer support
    protected bool $_isLayer = false;
    protected string $_layer = '';

    // Modern CSS support (Tailwind, Bootstrap, or any CSS framework)
    protected RenderMode $_renderMode = RenderMode::Classic;
    protected array $_cssClasses = [];
    protected ?ResponsiveWidth $_responsiveWidth = null;
    protected array $_responsiveClasses = []; // ['sm' => [...], 'md' => [...], 'lg' => [...]]
    protected string $_gap = '';
    protected string $_padding = '';
    protected string $_margin = '';
    protected string $_themeVariant = 'default';

    // Visual children
    public ?Collection $controls = null;

    // Popup menu
    protected mixed $_popupmenu = null;

    // PHP Events
    protected ?string $_onbeforeshow = null;
    protected ?string $_onaftershow = null;
    protected ?string $_onshow = null;

    // JavaScript Events - all 40+ event handlers
    protected ?string $_jsonactivate = null;
    protected ?string $_jsondeactivate = null;
    protected ?string $_jsonbeforecopy = null;
    protected ?string $_jsonbeforecut = null;
    protected ?string $_jsonbeforedeactivate = null;
    protected ?string $_jsonbeforeeditfocus = null;
    protected ?string $_jsonbeforepaste = null;
    protected ?string $_jsonblur = null;
    protected ?string $_jsonchange = null;
    protected ?string $_jsonclick = null;
    protected ?string $_jsoncontextmenu = null;
    protected ?string $_jsoncontrolselect = null;
    protected ?string $_jsoncopy = null;
    protected ?string $_jsoncut = null;
    protected ?string $_jsondblclick = null;
    protected ?string $_jsondrag = null;
    protected ?string $_jsondragenter = null;
    protected ?string $_jsondragleave = null;
    protected ?string $_jsondragover = null;
    protected ?string $_jsondragstart = null;
    protected ?string $_jsondrop = null;
    protected ?string $_jsonfilterchange = null;
    protected ?string $_jsonfocus = null;
    protected ?string $_jsonhelp = null;
    protected ?string $_jsonkeydown = null;
    protected ?string $_jsonkeypress = null;
    protected ?string $_jsonkeyup = null;
    protected ?string $_jsonlosecapture = null;
    protected ?string $_jsonmousedown = null;
    protected ?string $_jsonmouseup = null;
    protected ?string $_jsonmouseenter = null;
    protected ?string $_jsonmouseleave = null;
    protected ?string $_jsonmousemove = null;
    protected ?string $_jsonmouseout = null;
    protected ?string $_jsonmouseover = null;
    protected ?string $_jsonpaste = null;
    protected ?string $_jsonpropertychange = null;
    protected ?string $_jsonreadystatechange = null;
    protected ?string $_jsonresize = null;
    protected ?string $_jsonresizeend = null;
    protected ?string $_jsonresizestart = null;
    protected ?string $_jsonselectstart = null;

    // =========================================================================
    // PROPERTY HOOKS - Position
    // =========================================================================

    public int $Left {
        get => $this->_left;
        set => $this->_left = $value;
    }

    public int $Top {
        get => $this->_top;
        set => $this->_top = $value;
    }

    public ?int $Width {
        get => $this->_width;
        set {
            if ($value !== null && $value < 0) {
                $value = 0;
            }
            $this->_width = $value;
        }
    }

    public ?int $Height {
        get => $this->_height;
        set {
            if ($value !== null && $value < 0) {
                $value = 0;
            }
            $this->_height = $value;
        }
    }

    public int $Right {
        get => $this->_left + ($this->_width ?? 0);
    }

    public int $Bottom {
        get => $this->_top + ($this->_height ?? 0);
    }

    public int $ClientWidth {
        get => ($this->_width ?? 0) - $this->getBorderWidth() * 2;
    }

    public int $ClientHeight {
        get => ($this->_height ?? 0) - $this->getBorderWidth() * 2;
    }

    // =========================================================================
    // PROPERTY HOOKS - Appearance
    // =========================================================================

    public string $Caption {
        get => $this->_caption;
        set => $this->_caption = $value;
    }

    public string $Color {
        get {
            if ($this->_parentColor && $this->_parent !== null) {
                return $this->_parent->Color;
            }
            return $this->_color;
        }
        set {
            $this->_color = $value;
            $this->_parentColor = false;
        }
    }

    public string $Hint {
        get => $this->_hint;
        set => $this->_hint = $value;
    }

    public bool $Visible {
        get => $this->_visible;
        set => $this->_visible = $value;
    }

    public bool $Enabled {
        get => $this->_enabled;
        set => $this->_enabled = $value;
    }

    public bool $ShowHint {
        get {
            if ($this->_parentShowHint && $this->_parent !== null) {
                return $this->_parent->ShowHint;
            }
            return $this->_showHint;
        }
        set {
            $this->_showHint = $value;
            $this->_parentShowHint = false;
        }
    }

    public Cursor|string $Cursor {
        get => $this->_cursor;
        set => $this->_cursor = $value;
    }

    public string $Style {
        get => $this->_style;
        set => $this->_style = $value;
    }

    public bool $Hidden {
        get => $this->_hidden;
        set => $this->_hidden = $value;
    }

    public bool $DivWrap {
        get => $this->_divwrap;
        set => $this->_divwrap = $value;
    }

    public bool $Autosize {
        get => $this->_autosize;
        set => $this->_autosize = $value;
    }

    public string $AdjustToLayout {
        get => $this->_adjusttolayout;
        set => $this->_adjusttolayout = $value;
    }

    public array $Attributes {
        get => $this->_attributes;
        set => $this->_attributes = $value;
    }

    // =========================================================================
    // PROPERTY HOOKS - Layout
    // =========================================================================

    public ?Control $Parent {
        get => $this->_parent;
        set {
            if ($this->_parent === $value) {
                return;
            }

            if ($this->_parent !== null) {
                $this->_parent->removeChild($this);
            }

            $this->_parent = $value;

            if ($value !== null) {
                $value->addChild($this);
            }
        }
    }

    public Alignment|string $Align {
        get => $this->_align;
        set => $this->_align = $value;
    }

    public Anchors|string $Alignment {
        get => $this->_alignment;
        set => $this->_alignment = $value;
    }

    public bool $ParentFont {
        get => $this->_parentFont;
        set => $this->_parentFont = $value;
    }

    public bool $ParentColor {
        get => $this->_parentColor;
        set => $this->_parentColor = $value;
    }

    public bool $ParentShowHint {
        get => $this->_parentShowHint;
        set => $this->_parentShowHint = $value;
    }

    public bool $DoParentReset {
        get => $this->_doParentReset;
        set => $this->_doParentReset = $value;
    }

    public bool $IsLayer {
        get => $this->_isLayer;
        set => $this->_isLayer = $value;
    }

    public string $Layer {
        get => $this->_layer;
        set => $this->_layer = $value;
    }

    // =========================================================================
    // PROPERTY HOOKS - Modern CSS Framework Support
    // =========================================================================

    public RenderMode $RenderMode {
        get => $this->_renderMode;
        set => $this->_renderMode = $value;
    }

    /**
     * Additional CSS classes to apply to this control.
     * Framework-agnostic - works with Tailwind, Bootstrap, or custom CSS.
     */
    public array $Classes {
        get => $this->_cssClasses;
        set => $this->_cssClasses = $value;
    }

    public ?ResponsiveWidth $ResponsiveWidth {
        get => $this->_responsiveWidth;
        set => $this->_responsiveWidth = $value;
    }

    public array $ResponsiveClasses {
        get => $this->_responsiveClasses;
        set => $this->_responsiveClasses = $value;
    }

    public string $Gap {
        get => $this->_gap;
        set => $this->_gap = $value;
    }

    public string $Padding {
        get => $this->_padding;
        set => $this->_padding = $value;
    }

    public string $Margin {
        get => $this->_margin;
        set => $this->_margin = $value;
    }

    public string $ThemeVariant {
        get => $this->_themeVariant;
        set => $this->_themeVariant = $value;
    }

    public int $ControlCount {
        get => $this->controls?->count() ?? 0;
    }

    // =========================================================================
    // PROPERTY HOOKS - Font
    // =========================================================================

    public Font $Font {
        get {
            if ($this->_font === null) {
                $this->_font = new Font();
                $this->_font->_control = $this;
            }
            return $this->_font;
        }
        set {
            $this->_font = $value;
            $value->_control = $this;
        }
    }

    // =========================================================================
    // PROPERTY HOOKS - PHP Events
    // =========================================================================

    public ?string $OnBeforeShow {
        get => $this->_onbeforeshow;
        set => $this->_onbeforeshow = $value;
    }

    public ?string $OnAfterShow {
        get => $this->_onaftershow;
        set => $this->_onaftershow = $value;
    }

    public ?string $OnShow {
        get => $this->_onshow;
        set => $this->_onshow = $value;
    }

    // =========================================================================
    // PROPERTY HOOKS - JavaScript Events
    // =========================================================================

    public ?string $jsOnClick {
        get => $this->_jsonclick;
        set => $this->_jsonclick = $value;
    }

    public ?string $jsOnDblClick {
        get => $this->_jsondblclick;
        set => $this->_jsondblclick = $value;
    }

    public ?string $jsOnMouseDown {
        get => $this->_jsonmousedown;
        set => $this->_jsonmousedown = $value;
    }

    public ?string $jsOnMouseUp {
        get => $this->_jsonmouseup;
        set => $this->_jsonmouseup = $value;
    }

    public ?string $jsOnMouseMove {
        get => $this->_jsonmousemove;
        set => $this->_jsonmousemove = $value;
    }

    public ?string $jsOnMouseOver {
        get => $this->_jsonmouseover;
        set => $this->_jsonmouseover = $value;
    }

    public ?string $jsOnMouseOut {
        get => $this->_jsonmouseout;
        set => $this->_jsonmouseout = $value;
    }

    public ?string $jsOnMouseEnter {
        get => $this->_jsonmouseenter;
        set => $this->_jsonmouseenter = $value;
    }

    public ?string $jsOnMouseLeave {
        get => $this->_jsonmouseleave;
        set => $this->_jsonmouseleave = $value;
    }

    public ?string $jsOnKeyDown {
        get => $this->_jsonkeydown;
        set => $this->_jsonkeydown = $value;
    }

    public ?string $jsOnKeyUp {
        get => $this->_jsonkeyup;
        set => $this->_jsonkeyup = $value;
    }

    public ?string $jsOnKeyPress {
        get => $this->_jsonkeypress;
        set => $this->_jsonkeypress = $value;
    }

    public ?string $jsOnFocus {
        get => $this->_jsonfocus;
        set => $this->_jsonfocus = $value;
    }

    public ?string $jsOnBlur {
        get => $this->_jsonblur;
        set => $this->_jsonblur = $value;
    }

    public ?string $jsOnChange {
        get => $this->_jsonchange;
        set => $this->_jsonchange = $value;
    }

    public ?string $jsOnContextMenu {
        get => $this->_jsoncontextmenu;
        set => $this->_jsoncontextmenu = $value;
    }

    public ?string $jsOnResize {
        get => $this->_jsonresize;
        set => $this->_jsonresize = $value;
    }

    public ?string $jsOnDrag {
        get => $this->_jsondrag;
        set => $this->_jsondrag = $value;
    }

    public ?string $jsOnDragStart {
        get => $this->_jsondragstart;
        set => $this->_jsondragstart = $value;
    }

    public ?string $jsOnDragEnter {
        get => $this->_jsondragenter;
        set => $this->_jsondragenter = $value;
    }

    public ?string $jsOnDragOver {
        get => $this->_jsondragover;
        set => $this->_jsondragover = $value;
    }

    public ?string $jsOnDragLeave {
        get => $this->_jsondragleave;
        set => $this->_jsondragleave = $value;
    }

    public ?string $jsOnDrop {
        get => $this->_jsondrop;
        set => $this->_jsondrop = $value;
    }

    // =========================================================================
    // CONSTRUCTOR
    // =========================================================================

    public function __construct(?Component $owner = null)
    {
        $this->_font = new Font();
        $this->_font->_control = $this;
        $this->controls = new Collection();

        parent::__construct($owner);
    }

    // =========================================================================
    // CHILD CONTROLS
    // =========================================================================

    public function addChild(Control $child): void
    {
        if (!$this->controls->contains($child)) {
            $this->controls->add($child);
        }
    }

    public function removeChild(Control $child): void
    {
        $this->controls->remove($child);
    }

    public function getControl(int $index): ?Control
    {
        return $this->controls->get($index);
    }

    /**
     * @return \Generator<Control>
     */
    public function getControls(): \Generator
    {
        foreach ($this->controls->items as $child) {
            yield $child;
        }
    }

    // =========================================================================
    // RENDERING
    // =========================================================================

    protected function getBorderWidth(): int
    {
        return 0;
    }

    public function getInlineStyle(): string
    {
        return match ($this->_renderMode) {
            RenderMode::Classic => $this->getClassicInlineStyle(),
            RenderMode::Tailwind => $this->getMinimalInlineStyle(),
            RenderMode::Hybrid => $this->getHybridInlineStyle(),
        };
    }

    /**
     * Classic inline style: full positioning, sizing, and appearance.
     */
    protected function getClassicInlineStyle(): string
    {
        $styles = [];

        $alignValue = $this->_align instanceof Alignment ? $this->_align : Alignment::tryFrom($this->_align);

        if ($alignValue !== null && $alignValue !== Alignment::None) {
            $styles[] = 'position: ' . $alignValue->toCss();
        } else {
            $styles[] = "position: absolute";
            $styles[] = "left: {$this->_left}px";
            $styles[] = "top: {$this->_top}px";
        }

        if ($this->_width !== null) {
            $styles[] = "width: {$this->_width}px";
        }
        if ($this->_height !== null) {
            $styles[] = "height: {$this->_height}px";
        }

        if ($this->_color !== '') {
            $styles[] = "background-color: {$this->_color}";
        }

        $cursorValue = $this->_cursor instanceof Cursor ? $this->_cursor : Cursor::tryFrom($this->_cursor);
        if ($cursorValue !== null && $cursorValue !== Cursor::Default) {
            $styles[] = "cursor: {$cursorValue->toCss()}";
        }

        if (!$this->_visible || $this->_hidden) {
            $styles[] = "display: none";
        }

        return implode('; ', $styles);
    }

    /**
     * Minimal inline style for Tailwind mode: only visibility.
     * All other styling is handled by Tailwind classes.
     */
    protected function getMinimalInlineStyle(): string
    {
        $styles = [];

        if (!$this->_visible || $this->_hidden) {
            $styles[] = "display: none";
        }

        return implode('; ', $styles);
    }

    /**
     * Hybrid inline style: position via inline, appearance via Tailwind.
     */
    protected function getHybridInlineStyle(): string
    {
        $styles = [];

        $alignValue = $this->_align instanceof Alignment ? $this->_align : Alignment::tryFrom($this->_align);

        if ($alignValue !== null && $alignValue !== Alignment::None) {
            $styles[] = 'position: ' . $alignValue->toCss();
        } else {
            $styles[] = "position: absolute";
            $styles[] = "left: {$this->_left}px";
            $styles[] = "top: {$this->_top}px";
        }

        if ($this->_width !== null) {
            $styles[] = "width: {$this->_width}px";
        }
        if ($this->_height !== null) {
            $styles[] = "height: {$this->_height}px";
        }

        if (!$this->_visible || $this->_hidden) {
            $styles[] = "display: none";
        }

        return implode('; ', $styles);
    }

    /**
     * Get component type for theming (e.g., 'button', 'input', 'panel').
     * Override in subclasses to return specific component type.
     */
    protected function getComponentType(): string
    {
        return 'control';
    }

    /**
     * Get the theme class for this control based on component type and variant.
     */
    protected function getThemeClass(): string
    {
        $componentType = $this->getComponentType();
        $variant = $this->_themeVariant;

        if ($variant === 'default' || $variant === '') {
            return "vcl-{$componentType}";
        }

        return "vcl-{$componentType}-{$variant}";
    }

    /**
     * Build CSS classes string for this control.
     * Works with any CSS framework (Tailwind, Bootstrap, custom CSS).
     */
    public function getCssClasses(): string
    {
        if ($this->_renderMode === RenderMode::Classic) {
            return '';
        }

        $classes = [];

        // Add theme class
        $classes[] = $this->getThemeClass();

        // Add responsive width
        if ($this->_responsiveWidth !== null) {
            $classes[] = $this->_responsiveWidth->toTailwind();
        }

        // Add responsive classes for breakpoints
        foreach ($this->_responsiveClasses as $breakpoint => $breakpointClasses) {
            foreach ((array)$breakpointClasses as $class) {
                $classes[] = "{$breakpoint}:{$class}";
            }
        }

        // Add spacing classes
        if ($this->_gap !== '') {
            $classes[] = $this->_gap;
        }
        if ($this->_padding !== '') {
            $classes[] = $this->_padding;
        }
        if ($this->_margin !== '') {
            $classes[] = $this->_margin;
        }

        // Add custom CSS classes
        $classes = array_merge($classes, $this->_cssClasses);

        return implode(' ', array_filter($classes));
    }

    // Legacy alias
    public function getStyle(): string
    {
        return $this->getInlineStyle();
    }

    public function getHTMLAttributes(): string
    {
        $attrs = [];

        if ($this->_name !== '') {
            $attrs[] = 'id="' . htmlspecialchars($this->_name) . '"';
        }

        $style = $this->getInlineStyle();
        if ($style !== '') {
            $attrs[] = 'style="' . htmlspecialchars($style) . '"';
        }

        // Build class attribute from Style property and Tailwind classes
        $classNames = [];

        if ($this->_style !== '') {
            $styleClass = $this->_style;
            if (str_starts_with($styleClass, '.')) {
                $styleClass = substr($styleClass, 1);
            }
            $classNames[] = $styleClass;
        }

        // Add CSS classes if in Tailwind or Hybrid mode
        $cssClasses = $this->getCssClasses();
        if ($cssClasses !== '') {
            $classNames[] = $cssClasses;
        }

        if (!empty($classNames)) {
            $attrs[] = 'class="' . htmlspecialchars(implode(' ', $classNames)) . '"';
        }

        if (!$this->_enabled) {
            $attrs[] = 'disabled';
        }

        if ($this->_hint !== '' && $this->ShowHint) {
            $attrs[] = 'title="' . htmlspecialchars($this->_hint) . '"';
        }

        // Add JavaScript events
        $attrs[] = $this->getJSEventAttributes();

        // Add custom attributes
        foreach ($this->_attributes as $name => $value) {
            $attrs[] = htmlspecialchars($name) . '="' . htmlspecialchars($value) . '"';
        }

        return implode(' ', array_filter($attrs));
    }

    // Legacy alias
    public function getAttributes(): string
    {
        return $this->getHTMLAttributes();
    }

    protected function getJSEventAttributes(): string
    {
        $events = [];

        if ($this->_jsonclick) {
            $events[] = 'onclick="' . htmlspecialchars($this->_jsonclick) . '"';
        }
        if ($this->_jsondblclick) {
            $events[] = 'ondblclick="' . htmlspecialchars($this->_jsondblclick) . '"';
        }
        if ($this->_jsonmousedown) {
            $events[] = 'onmousedown="' . htmlspecialchars($this->_jsonmousedown) . '"';
        }
        if ($this->_jsonmouseup) {
            $events[] = 'onmouseup="' . htmlspecialchars($this->_jsonmouseup) . '"';
        }
        if ($this->_jsonmousemove) {
            $events[] = 'onmousemove="' . htmlspecialchars($this->_jsonmousemove) . '"';
        }
        if ($this->_jsonmouseover) {
            $events[] = 'onmouseover="' . htmlspecialchars($this->_jsonmouseover) . '"';
        }
        if ($this->_jsonmouseout) {
            $events[] = 'onmouseout="' . htmlspecialchars($this->_jsonmouseout) . '"';
        }
        if ($this->_jsonmouseenter) {
            $events[] = 'onmouseenter="' . htmlspecialchars($this->_jsonmouseenter) . '"';
        }
        if ($this->_jsonmouseleave) {
            $events[] = 'onmouseleave="' . htmlspecialchars($this->_jsonmouseleave) . '"';
        }
        if ($this->_jsonkeydown) {
            $events[] = 'onkeydown="' . htmlspecialchars($this->_jsonkeydown) . '"';
        }
        if ($this->_jsonkeyup) {
            $events[] = 'onkeyup="' . htmlspecialchars($this->_jsonkeyup) . '"';
        }
        if ($this->_jsonkeypress) {
            $events[] = 'onkeypress="' . htmlspecialchars($this->_jsonkeypress) . '"';
        }
        if ($this->_jsonfocus) {
            $events[] = 'onfocus="' . htmlspecialchars($this->_jsonfocus) . '"';
        }
        if ($this->_jsonblur) {
            $events[] = 'onblur="' . htmlspecialchars($this->_jsonblur) . '"';
        }
        if ($this->_jsonchange) {
            $events[] = 'onchange="' . htmlspecialchars($this->_jsonchange) . '"';
        }
        if ($this->_jsoncontextmenu) {
            $events[] = 'oncontextmenu="' . htmlspecialchars($this->_jsoncontextmenu) . '"';
        }
        if ($this->_jsonresize) {
            $events[] = 'onresize="' . htmlspecialchars($this->_jsonresize) . '"';
        }

        return implode(' ', $events);
    }

    public function render(): string
    {
        return sprintf(
            '<div %s>%s</div>',
            $this->getHTMLAttributes(),
            htmlspecialchars($this->_caption)
        );
    }

    public function show(): void
    {
        if (!$this->canShow()) {
            return;
        }

        $this->callEvent('OnBeforeShow', []);
        echo $this->render();
        $this->callEvent('OnAfterShow', []);
    }

    public function canShow(): bool
    {
        return $this->_visible;
    }

    public function setBounds(int $left, int $top, int $width, int $height): void
    {
        $this->_left = $left;
        $this->_top = $top;
        $this->_width = $width;
        $this->_height = $height;
    }

    public function readStyleClass(): string
    {
        if ($this->_style !== '') {
            $res = $this->_style;
            if (str_starts_with($res, '.')) {
                $res = substr($res, 1);
            }
            return $res;
        }
        return '';
    }

    // =========================================================================
    // FONT UPDATES
    // =========================================================================

    public function updateChildrenFonts(): void
    {
        if ($this->controls === null) {
            return;
        }

        foreach ($this->controls->items as $child) {
            if ($child instanceof Control && $child->ParentFont) {
                $child->_font = null; // Force refresh
            }
        }
    }

    /**
     * Update font from parent control.
     */
    public function updateParentFont(): void
    {
        if ($this->_parentFont && $this->_parent !== null) {
            // Force font refresh by clearing cached font
            $this->_font = null;
        }
    }

    /**
     * Update color from parent control.
     */
    public function updateParentColor(): void
    {
        // Color is dynamically resolved via ParentColor, nothing to update
    }

    /**
     * Update ShowHint from parent control.
     */
    public function updateParentShowHint(): void
    {
        // ShowHint is dynamically resolved via ParentShowHint, nothing to update
    }

    /**
     * Update all parent-dependent properties.
     */
    public function updateParentProperties(): void
    {
        $this->updateParentFont();
        $this->updateParentColor();
        $this->updateParentShowHint();
    }

    // =========================================================================
    // JAVASCRIPT WRAPPER METHODS
    // =========================================================================

    /**
     * Get the hidden field name for JavaScript wrapper events.
     */
    protected function readJSWrapperHiddenFieldName(): string
    {
        return $this->_name . '_event';
    }

    /**
     * Get the submit event value for JavaScript wrapper.
     */
    protected function readJSWrapperSubmitEventValue(?string $event): string
    {
        return $event ?? '';
    }

    /**
     * Get the JavaScript wrapper function code.
     * @param string|null $eventName Optional event name to generate function for
     */
    protected function getJSWrapperFunction(?string $eventName = null): string
    {
        if ($eventName === null) {
            return $this->_name . '_event_wrapper';
        }

        // Generate JavaScript wrapper function code
        $hiddenField = $this->readJSWrapperHiddenFieldName();
        return <<<JS
function {$eventName}(event) {
    var hiddenField = document.getElementById('{$hiddenField}');
    if (hiddenField) hiddenField.value = '{$eventName}';
    document.forms[0].submit();
}

JS;
    }

    /**
     * Read JavaScript events as HTML attributes.
     */
    protected function readJsEvents(): string
    {
        $events = [];

        if ($this->_jsonclick !== null) {
            $events[] = 'onclick="' . htmlspecialchars($this->_jsonclick) . '(event)"';
        }
        if ($this->_jsondblclick !== null) {
            $events[] = 'ondblclick="' . htmlspecialchars($this->_jsondblclick) . '(event)"';
        }

        return implode(' ', $events);
    }

    /**
     * Add JavaScript wrapper to events string.
     */
    protected function addJSWrapperToEvents(string &$events, ?string $phpEvent, ?string $jsEvent, string $eventName): void
    {
        if ($phpEvent !== null || $jsEvent !== null) {
            $wrapper = $this->getJSWrapperFunction();
            $events .= ' ' . $eventName . '="' . $wrapper . '(event)"';
        }
    }

    /**
     * Get the hint attribute for HTML elements.
     */
    protected function getHintAttribute(): string
    {
        if ($this->_showHint && $this->_hint !== '') {
            return ' title="' . htmlspecialchars($this->_hint) . '"';
        }
        return '';
    }

    // =========================================================================
    // LEGACY GETTERS/SETTERS
    // =========================================================================

    public function getLeft(): int { return $this->_left; }
    public function setLeft(int $value): void { $this->Left = $value; }
    public function defaultLeft(): int { return 0; }

    public function getTop(): int { return $this->_top; }
    public function setTop(int $value): void { $this->Top = $value; }
    public function defaultTop(): int { return 0; }

    public function getWidth(): ?int { return $this->_width; }
    public function setWidth(?int $value): void { $this->Width = $value; }
    public function defaultWidth(): ?int { return null; }

    public function getHeight(): ?int { return $this->_height; }
    public function setHeight(?int $value): void { $this->Height = $value; }
    public function defaultHeight(): ?int { return null; }

    public function getCaption(): string { return $this->_caption; }
    public function setCaption(string $value): void { $this->Caption = $value; }
    public function defaultCaption(): string { return ''; }

    public function getColor(): string { return $this->Color; }
    public function setColor(string $value): void { $this->Color = $value; }
    public function defaultColor(): string { return ''; }

    public function getVisible(): bool { return $this->_visible; }
    public function setVisible(bool $value): void { $this->Visible = $value; }
    public function defaultVisible(): int { return 1; }

    public function getEnabled(): bool { return $this->_enabled; }
    public function setEnabled(bool $value): void { $this->Enabled = $value; }
    public function defaultEnabled(): int { return 1; }

    public function getHint(): string { return $this->_hint; }
    public function setHint(string $value): void { $this->Hint = $value; }
    public function defaultHint(): string { return ''; }

    public function getShowHint(): bool { return $this->ShowHint; }
    public function setShowHint(bool $value): void { $this->ShowHint = $value; }
    public function defaultShowHint(): int { return 0; }

    public function getFont(): Font { return $this->Font; }
    public function setFont(Font $value): void { $this->Font = $value; }

    public function getParent(): ?Control { return $this->_parent; }
    public function setParent(?Control $value): void { $this->Parent = $value; }

    public function getAlign(): Alignment|string { return $this->_align; }
    public function setAlign(Alignment|string $value): void { $this->Align = $value; }
    public function defaultAlign(): string { return 'alNone'; }

    public function getAlignment(): Anchors|string { return $this->_alignment; }
    public function setAlignment(Anchors|string $value): void { $this->Alignment = $value; }
    public function defaultAlignment(): string { return 'agNone'; }

    public function getCursor(): Cursor|string { return $this->_cursor; }
    public function setCursor(Cursor|string $value): void { $this->Cursor = $value; }
    public function defaultCursor(): string { return ''; }

    public function getParentFont(): bool { return $this->_parentFont; }
    public function setParentFont(bool $value): void { $this->ParentFont = $value; }
    public function defaultParentFont(): int { return 1; }

    public function getParentColor(): bool { return $this->_parentColor; }
    public function setParentColor(bool $value): void { $this->ParentColor = $value; }
    public function defaultParentColor(): int { return 1; }

    public function getParentShowHint(): bool { return $this->_parentShowHint; }
    public function setParentShowHint(bool $value): void { $this->ParentShowHint = $value; }
    public function defaultParentShowHint(): int { return 1; }

    public function getIsLayer(): bool { return $this->_isLayer; }
    public function setIsLayer(bool $value): void { $this->IsLayer = $value; }
    public function defaultIsLayer(): int { return 0; }

    public function getPopupMenu(): mixed { return $this->_popupmenu; }
    public function setPopupMenu(mixed $value): void { $this->_popupmenu = $value; }

    public function readControls(): Collection { return $this->controls; }
    public function readControlCount(): int { return $this->controls->count(); }

    // JS Event legacy getters
    public function getjsOnClick(): ?string { return $this->_jsonclick; }
    public function setjsOnClick(?string $value): void { $this->jsOnClick = $value; }
    public function defaultjsOnClick(): ?string { return null; }

    public function getjsOnDblClick(): ?string { return $this->_jsondblclick; }
    public function setjsOnDblClick(?string $value): void { $this->jsOnDblClick = $value; }
    public function defaultjsOnDblClick(): ?string { return null; }

    // ... additional JS event getters can be added as needed
}
