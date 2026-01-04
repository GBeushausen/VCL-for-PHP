<?php
/**
 * VCL for PHP 3.0
 *
 * FlexPanel - A responsive flex container using Tailwind CSS
 */

declare(strict_types=1);

namespace VCL\ExtCtrls;

use VCL\UI\Enums\Alignment;
use VCL\UI\Enums\AlignItems;
use VCL\UI\Enums\FlexDirection;
use VCL\UI\Enums\FlexWrap;
use VCL\UI\Enums\JustifyContent;
use VCL\UI\Enums\RenderMode;

/**
 * FlexPanel is a container that uses CSS Flexbox for layout.
 *
 * This component renders its children in a flex container with configurable
 * direction, wrapping, and alignment properties using Tailwind CSS classes.
 *
 * Child controls' Align property is mapped to flex behavior:
 * - alTop/alLeft: align-self: flex-start
 * - alBottom/alRight: align-self: flex-end
 * - alClient: flex: 1 (grow to fill available space)
 * - alNone: use control's explicit positioning
 */
class FlexPanel extends CustomPanel
{
    protected FlexDirection $_direction = FlexDirection::Row;
    protected FlexWrap $_wrap = FlexWrap::NoWrap;
    protected JustifyContent $_justifyContent = JustifyContent::Start;
    protected AlignItems $_alignItems = AlignItems::Stretch;
    protected string $_flexGap = 'gap-4';
    protected array $_responsiveDirection = []; // ['md' => FlexDirection::Row]

    // Property Hooks
    public FlexDirection $Direction {
        get => $this->_direction;
        set => $this->_direction = $value;
    }

    public FlexWrap $Wrap {
        get => $this->_wrap;
        set => $this->_wrap = $value;
    }

    public JustifyContent $JustifyContent {
        get => $this->_justifyContent;
        set => $this->_justifyContent = $value;
    }

    public AlignItems $AlignItems {
        get => $this->_alignItems;
        set => $this->_alignItems = $value;
    }

    public string $FlexGap {
        get => $this->_flexGap;
        set => $this->_flexGap = $value;
    }

    public array $ResponsiveDirection {
        get => $this->_responsiveDirection;
        set => $this->_responsiveDirection = $value;
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        // FlexPanel always uses Tailwind mode
        $this->_renderMode = RenderMode::Tailwind;
    }

    protected function getComponentType(): string
    {
        return 'flex';
    }

    /**
     * Build the flex container classes.
     */
    protected function getFlexClasses(): string
    {
        $classes = ['flex'];

        // Direction
        $classes[] = $this->_direction->toTailwind();

        // Responsive directions
        foreach ($this->_responsiveDirection as $breakpoint => $direction) {
            if ($direction instanceof FlexDirection) {
                $classes[] = "{$breakpoint}:{$direction->toTailwind()}";
            }
        }

        // Wrap
        $classes[] = $this->_wrap->toTailwind();

        // Justify content
        $classes[] = $this->_justifyContent->toTailwind();

        // Align items
        $classes[] = $this->_alignItems->toTailwind();

        // Gap
        if ($this->_flexGap !== '') {
            $classes[] = $this->_flexGap;
        }

        return implode(' ', $classes);
    }

    /**
     * Map Alignment enum to flex child classes.
     */
    protected function getFlexChildClasses(Alignment|string $align): string
    {
        $alignValue = $align instanceof Alignment ? $align : Alignment::tryFrom($align);

        if ($alignValue === null || $alignValue === Alignment::None) {
            return '';
        }

        // Determine behavior based on direction
        $isHorizontal = $this->_direction->isHorizontal();

        return match ($alignValue) {
            Alignment::Top => $isHorizontal ? 'self-start' : '',
            Alignment::Bottom => $isHorizontal ? 'self-end' : '',
            Alignment::Left => $isHorizontal ? '' : 'self-start',
            Alignment::Right => $isHorizontal ? '' : 'self-end',
            Alignment::Client => 'flex-1',
            Alignment::Custom => '',
            default => '',
        };
    }

    /**
     * Render the flex panel.
     */
    protected function dumpContents(): void
    {
        $name = htmlspecialchars($this->Name);

        // Build class list
        $classes = [];
        $classes[] = $this->getFlexClasses();

        // Add padding/margin from Control
        if ($this->_padding !== '') {
            $classes[] = $this->_padding;
        }
        if ($this->_margin !== '') {
            $classes[] = $this->_margin;
        }

        // Add custom Tailwind classes
        if (!empty($this->_cssClasses)) {
            $classes = array_merge($classes, $this->_cssClasses);
        }

        // Add Style property class
        $styleClass = $this->readStyleClass();
        if ($styleClass !== '') {
            $classes[] = $styleClass;
        }

        $classAttr = 'class="' . htmlspecialchars(implode(' ', array_filter($classes))) . '"';

        // Build inline style (minimal for Tailwind mode)
        $style = $this->getInlineStyle();
        $styleAttr = $style !== '' ? " style=\"{$style}\"" : '';

        echo "<div id=\"{$name}\" {$classAttr}{$styleAttr}>\n";

        // Render child controls
        if ($this->controls !== null) {
            foreach ($this->controls->items as $child) {
                if (!$child->Visible) {
                    continue;
                }

                // Wrap child in flex item container with alignment
                $childClasses = $this->getFlexChildClasses($child->Align);

                if ($childClasses !== '') {
                    echo "<div class=\"{$childClasses}\">\n";
                }

                if (method_exists($child, 'show')) {
                    $child->show();
                } elseif (method_exists($child, 'dumpContents')) {
                    $child->dumpContents();
                } else {
                    echo $child->render();
                }

                if ($childClasses !== '') {
                    echo "</div>\n";
                }
            }
        }

        echo "</div>\n";
    }

    // Legacy getters/setters
    public function getDirection(): FlexDirection { return $this->_direction; }
    public function setDirection(FlexDirection $value): void { $this->Direction = $value; }

    public function getWrap(): FlexWrap { return $this->_wrap; }
    public function setWrap(FlexWrap $value): void { $this->Wrap = $value; }

    public function getJustifyContent(): JustifyContent { return $this->_justifyContent; }
    public function setJustifyContent(JustifyContent $value): void { $this->JustifyContent = $value; }

    public function getAlignItems(): AlignItems { return $this->_alignItems; }
    public function setAlignItems(AlignItems $value): void { $this->AlignItems = $value; }

    public function getFlexGap(): string { return $this->_flexGap; }
    public function setFlexGap(string $value): void { $this->FlexGap = $value; }

    public function getResponsiveDirection(): array { return $this->_responsiveDirection; }
    public function setResponsiveDirection(array $value): void { $this->ResponsiveDirection = $value; }
}
