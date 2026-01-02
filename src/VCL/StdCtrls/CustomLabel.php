<?php

declare(strict_types=1);

namespace VCL\StdCtrls;

use VCL\UI\GraphicControl;
use VCL\UI\Enums\Anchors;

/**
 * CustomLabel is the base class for label components.
 *
 * Labels display text that the user cannot edit directly.
 *
 * PHP 8.4 version with Property Hooks.
 */
class CustomLabel extends GraphicControl
{
    protected mixed $_datasource = null;
    protected string $_datafield = '';
    protected string $_link = '';
    protected string $_linktarget = '';
    protected bool $_wordwrap = true;
    protected string $_formatasdate = '';
    protected bool $_htmlcontent = false;

    // Events
    protected ?string $_onclick = null;
    protected ?string $_ondblclick = null;

    // Property Hooks
    public mixed $DataSource {
        get => $this->_datasource;
        set => $this->_datasource = $value;
    }

    public string $DataField {
        get => $this->_datafield;
        set => $this->_datafield = $value;
    }

    public string $Link {
        get => $this->_link;
        set => $this->_link = $value;
    }

    public string $LinkTarget {
        get => $this->_linktarget;
        set => $this->_linktarget = $value;
    }

    public bool $WordWrap {
        get => $this->_wordwrap;
        set => $this->_wordwrap = $value;
    }

    public string $FormatAsDate {
        get => $this->_formatasdate;
        set => $this->_formatasdate = $value;
    }

    public ?string $OnClick {
        get => $this->_onclick;
        set => $this->_onclick = $value;
    }

    public ?string $OnDblClick {
        get => $this->_ondblclick;
        set => $this->_ondblclick = $value;
    }

    public bool $HtmlContent {
        get => $this->_htmlcontent;
        set => $this->_htmlcontent = $value;
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->_width = 75;
        $this->_height = 13;
        $this->_controlstyle['csRenderOwner'] = true;
        $this->_controlstyle['csRenderAlso'] = 'StyleSheet';
    }

    /**
     * Get the display caption, formatted if necessary.
     */
    public function getDisplayCaption(): string
    {
        $caption = $this->Caption;

        // Apply data source if set
        if ($this->_datasource !== null && $this->_datafield !== '') {
            // Data binding logic would go here
        }

        // Apply date formatting if set
        if ($this->_formatasdate !== '' && $caption !== '') {
            $timestamp = strtotime($caption);
            if ($timestamp !== false) {
                $caption = date($this->_formatasdate, $timestamp);
            }
        }

        return $caption;
    }

    /**
     * Render the label.
     */
    public function dumpContents(): void
    {
        $styles = [];

        // Build style string
        if ($this->_style === '') {
            // Font styles
            if ($this->_font !== null) {
                $styles[] = rtrim($this->Font->readFontString(), ';');
            }

            // Background color
            if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING && $this->_designcolor !== '') {
                $styles[] = "background-color: {$this->_designcolor}";
            } elseif ($this->Color !== '') {
                $styles[] = "background-color: {$this->Color}";
            }

            // Cursor
            $cursorValue = $this->_cursor;
            if ($cursorValue instanceof \VCL\UI\Enums\Cursor) {
                $cursorValue = $cursorValue->value;
            }
            if ($cursorValue !== '' && $cursorValue !== 'crDefault') {
                $cursor = strtolower(substr($cursorValue, 2));
                $styles[] = "cursor: {$cursor}";
            }
        }

        // Size
        if (!$this->_autosize) {
            if (!$this->_adjusttolayout) {
                if ($this->Height > 0) {
                    $styles[] = "height: {$this->Height}px";
                }
                if ($this->Width > 0) {
                    $styles[] = "width: {$this->Width}px";
                }
            } else {
                $styles[] = "height: 100%";
                $styles[] = "width: 100%";
            }
        }

        // Word wrap
        if (!$this->_wordwrap) {
            $styles[] = "white-space: nowrap";
        }

        // Visibility
        if ($this->Hidden && ($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
            $styles[] = "visibility: hidden";
        }

        // Build alignment attribute
        $alignment = '';
        $alignValue = $this->_alignment;
        if ($alignValue instanceof Anchors) {
            $alignValue = $alignValue->value;
        }

        $alignment = match ($alignValue) {
            'agLeft', Anchors::Left->value => ' align="left"',
            'agCenter', Anchors::Center->value => ' align="center"',
            'agRight', Anchors::Right->value => ' align="right"',
            default => '',
        };

        // Build class attribute
        $class = $this->readStyleClass();
        $classAttr = $class !== '' ? " class=\"{$class}\"" : '';

        // Build style attribute
        $style = implode('; ', $styles);
        $styleAttr = $style !== '' ? " style=\"{$style}\"" : '';

        // Build events
        $events = $this->getJSEventAttributes();

        // Get caption
        $caption = $this->getDisplayCaption();

        // Build link if set
        if ($this->_link !== '') {
            $target = $this->_linktarget !== '' ? " target=\"{$this->_linktarget}\"" : '';
            $caption = sprintf(
                '<a href="%s"%s>%s</a>',
                htmlspecialchars($this->_link),
                $target,
                $this->_htmlcontent ? $caption : htmlspecialchars($caption)
            );
        } else {
            // Only escape if not HTML content
            if (!$this->_htmlcontent) {
                $caption = htmlspecialchars($caption);
            }
        }

        echo sprintf(
            '<div id="%s"%s%s%s%s>%s</div>',
            htmlspecialchars($this->Name),
            $classAttr,
            $styleAttr,
            $alignment,
            $events !== '' ? ' ' . $events : '',
            $caption
        );
    }

    /**
     * Override render.
     */
    public function render(): string
    {
        ob_start();
        $this->dumpContents();
        return ob_get_clean();
    }

    // Legacy getters/setters
    public function readDataSource(): mixed { return $this->_datasource; }
    public function writeDataSource(mixed $value): void { $this->DataSource = $value; }

    public function readDataField(): string { return $this->_datafield; }
    public function writeDataField(string $value): void { $this->DataField = $value; }
    public function defaultDataField(): string { return ''; }

    public function getLink(): string { return $this->_link; }
    public function setLink(string $value): void { $this->Link = $value; }
    public function defaultLink(): string { return ''; }

    public function getLinkTarget(): string { return $this->_linktarget; }
    public function setLinkTarget(string $value): void { $this->LinkTarget = $value; }
    public function defaultLinkTarget(): string { return ''; }

    public function getWordWrap(): bool { return $this->_wordwrap; }
    public function setWordWrap(bool $value): void { $this->WordWrap = $value; }
    public function defaultWordWrap(): int { return 1; }

    public function readFormatAsDate(): string { return $this->_formatasdate; }
    public function writeFormatAsDate(string $value): void { $this->FormatAsDate = $value; }
    public function defaultFormatAsDate(): string { return ''; }

    public function getHtmlContent(): bool { return $this->_htmlcontent; }
    public function setHtmlContent(bool $value): void { $this->HtmlContent = $value; }
    public function defaultHtmlContent(): bool { return false; }
}
