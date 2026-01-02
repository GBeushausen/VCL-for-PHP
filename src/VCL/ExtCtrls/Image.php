<?php

declare(strict_types=1);

namespace VCL\ExtCtrls;

use VCL\UI\FocusControl;

/**
 * Image displays a graphical image on a form.
 *
 * Use Image to display a graphical image on a form. The image can be
 * loaded from a file, data source, or binary data. Supports linking,
 * proportional scaling, stretching, and centering.
 *
 * PHP 8.4 version with Property Hooks.
 */
class Image extends FocusControl
{
    protected bool $_autosize = false;
    protected bool $_border = false;
    protected string $_bordercolor = '';
    protected bool $_center = false;
    protected string $_datafield = '';
    protected mixed $_datasource = null;
    protected string $_imagesource = '';
    protected string $_link = '';
    protected string $_linktarget = '';
    protected bool $_proportional = false;
    protected bool $_stretch = false;
    protected bool $_binary = false;
    protected string $_binarytype = 'image/jpeg';

    // Events
    protected ?string $_onclick = null;
    protected ?string $_oncustomize = null;

    // Property Hooks
    public bool $AutoSize {
        get => $this->_autosize;
        set => $this->_autosize = $value;
    }

    public bool $Border {
        get => $this->_border;
        set => $this->_border = $value;
    }

    public string $BorderColor {
        get => $this->_bordercolor;
        set => $this->_bordercolor = $value;
    }

    public bool $Center {
        get => $this->_center;
        set => $this->_center = $value;
    }

    public string $DataField {
        get => $this->_datafield;
        set => $this->_datafield = $value;
    }

    public mixed $DataSource {
        get => $this->_datasource;
        set => $this->_datasource = $value;
    }

    public string $ImageSource {
        get => $this->_imagesource;
        set => $this->_imagesource = $value;
    }

    public string $Link {
        get => $this->_link;
        set => $this->_link = $value;
    }

    public string $LinkTarget {
        get => $this->_linktarget;
        set => $this->_linktarget = $value;
    }

    public bool $Proportional {
        get => $this->_proportional;
        set => $this->_proportional = $value;
    }

    public bool $Stretch {
        get => $this->_stretch;
        set => $this->_stretch = $value;
    }

    public bool $Binary {
        get => $this->_binary;
        set => $this->_binary = $value;
    }

    public string $BinaryType {
        get => $this->_binarytype;
        set => $this->_binarytype = $value;
    }

    public ?string $OnClick {
        get => $this->_onclick;
        set => $this->_onclick = $value;
    }

    public ?string $OnCustomize {
        get => $this->_oncustomize;
        set => $this->_oncustomize = $value;
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->_width = 105;
        $this->_height = 105;
        $this->_controlstyle['csAcceptsControls'] = true;
        $this->_controlstyle['csRenderOwner'] = true;
        $this->_controlstyle['csRenderAlso'] = 'StyleSheet';
    }

    /**
     * Get the absolute image path.
     */
    private function getImageSourcePath(): string
    {
        if ($this->_imagesource === '') {
            return '';
        }

        // Check if relative
        if (str_starts_with($this->_imagesource, '..') || str_starts_with($this->_imagesource, '.')) {
            return dirname($_SERVER['SCRIPT_FILENAME'] ?? '') . '/' . $this->_imagesource;
        }

        return $this->_imagesource;
    }

    /**
     * Called when component is loaded.
     */
    public function loaded(): void
    {
        parent::loaded();

        if ($this->_datasource !== null) {
            $this->_datasource = $this->fixupProperty($this->_datasource);
        }

        if ($this->_autosize && $this->_imagesource !== '') {
            $path = $this->getImageSourcePath();
            if ($path !== '' && is_file($path)) {
                $result = getimagesize($path);
                if (is_array($result)) {
                    $bordersize = $this->_border ? 2 : 0;
                    [$width, $height] = $result;
                    $this->_width = $width + $bordersize;
                    $this->_height = $height + $bordersize;
                }
            }
        }
    }

    /**
     * Initialize and handle events.
     */
    public function init(): void
    {
        parent::init();

        $hiddenName = $this->readJSWrapperHiddenFieldName();
        $submitEventValue = $this->input->$hiddenName ?? null;

        if (is_object($submitEventValue) && method_exists($submitEventValue, 'asString')) {
            if ($this->_onclick !== null) {
                $expectedValue = $this->readJSWrapperSubmitEventValue($this->_onclick);
                if ($submitEventValue->asString() === $expectedValue) {
                    $this->callEvent('onclick', []);
                }
            }
        }

        // Check for binary image request
        if ($this->_binary && isset($this->owner)) {
            $key = md5($this->owner->Name . $this->Name . $this->Left . $this->Top . $this->Width . $this->Height);
            $bimg = $this->input->bimg ?? null;
            if (is_object($bimg) && method_exists($bimg, 'asString') && $bimg->asString() === $key) {
                $this->dumpGraphic();
            }
        }
    }

    /**
     * Check if a valid data field is configured.
     */
    protected function hasValidDataField(): bool
    {
        return $this->_datafield !== '' &&
               $this->_datasource !== null &&
               is_object($this->_datasource) &&
               isset($this->_datasource->DataSet);
    }

    /**
     * Read the value from the configured data field.
     */
    protected function readDataFieldValue(): mixed
    {
        if (!$this->hasValidDataField()) {
            return null;
        }

        $dataset = $this->_datasource->DataSet;
        if ($dataset !== null && method_exists($dataset, 'fieldget')) {
            return $dataset->fieldget($this->_datafield);
        }

        return null;
    }

    /**
     * Dump the graphic as binary.
     */
    public function dumpGraphic(): void
    {
        header("Content-type: {$this->_binarytype}");
        header("Pragma: no-cache");
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

        if ($this->hasValidDataField()) {
            echo $this->readDataFieldValue();
        }

        exit;
    }

    /**
     * Render the image.
     */
    public function dumpContents(): void
    {
        if ($this->_onshow !== null) {
            $this->callEvent('onshow', []);
            return;
        }

        $divstyle = '';
        $imgstyle = '';
        $attr = '';
        $imagecoords = false;
        $iwidth = 0;
        $iheight = 0;

        // Map for child controls
        $map = '';
        if ($this->controls->count() > 0) {
            $map = "usemap=\"#map{$this->Name}\"";
        }

        // Events
        $events = $this->readJsEvents();
        $this->addJSWrapperToEvents($events, $this->_onclick, $this->_jsonclick ?? null, 'onclick');

        // Get image dimensions if file exists
        if ($this->_imagesource !== '') {
            $path = $this->getImageSourcePath();
            if ($path !== '' && is_file($path)) {
                $result = getimagesize($path);
                if (is_array($result)) {
                    [$iwidth, $iheight] = $result;
                    $imagecoords = true;
                }
            }
        }

        // Div styles
        $divstyle .= "width:{$this->_width}px;height:{$this->_height}px;";

        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            $divstyle .= "border:1px dashed gray;";
        }

        // Cursor
        $cursorValue = $this->_cursor;
        if ($cursorValue instanceof \VCL\UI\Enums\Cursor) {
            $cursorValue = $cursorValue->value;
        }
        if ($cursorValue !== '' && $cursorValue !== 'crDefault' && $this->_style === '') {
            $cursor = strtolower(substr($cursorValue, 2));
            $divstyle .= "cursor:{$cursor};";
        }

        // Size attributes based on stretch/proportional
        if (!$this->_stretch && !$this->_proportional) {
            $divstyle .= "overflow:hidden;";
            if ($imagecoords) {
                $attr .= " width=\"{$iwidth}\" height=\"{$iheight}\"";
            } else {
                $attr .= " width=\"{$this->_width}\" height=\"{$this->_height}\"";
            }
        }

        if ($this->_stretch && !$this->_proportional) {
            $attr .= " width=\"{$this->_width}\" height=\"{$this->_height}\"";
        }

        if ($this->_proportional && $imagecoords && $iheight > 0 && $iwidth > 0) {
            $hratio = $iwidth / $iheight;
            $twidth = $this->_height * $hratio;

            if ($twidth < $this->_width) {
                $attr .= " height=\"{$this->_height}\"";
            } else {
                $attr .= " width=\"{$this->_width}\"";
            }
        } elseif ($this->_proportional) {
            $attr .= " width=\"{$this->_width}\" height=\"{$this->_height}\"";
        }

        // Center
        if ($this->_center) {
            $divstyle .= "text-align:center;";
            $margin = (int) floor(($this->_height - $iheight) / 2);
            $imgstyle .= "margin-top:{$margin}px;";
        }

        // Hint
        $hint = '';
        if ($this->_hint !== '' && $this->_showHint) {
            $hintEsc = htmlspecialchars($this->_hint, ENT_QUOTES);
            $hint = " title=\"{$hintEsc}\" alt=\"{$hintEsc}\"";
        }

        // Border
        if ($this->_style === '') {
            $borderVal = $this->_border ? '1' : '0';
            $attr .= " border=\"{$borderVal}\"";
            if ($this->_bordercolor !== '') {
                $attr .= " style=\"border-color:{$this->_bordercolor}\"";
            }
        }

        // Class
        $class = $this->readStyleClass();
        $classAttr = $class !== '' ? " class=\"{$class}\"" : '';

        // Hidden
        if ($this->Hidden && ($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
            $divstyle .= "visibility:hidden;";
        }

        $name = htmlspecialchars($this->Name);

        echo "<div id=\"{$name}_container\" style=\"{$divstyle}\"{$classAttr}>";

        // Link
        if ($this->_link !== '') {
            $linkEsc = htmlspecialchars($this->_link);
            $target = $this->_linktarget !== '' ? " target=\"{$this->_linktarget}\"" : '';
            echo "<a href=\"{$linkEsc}\"{$target}{$hint}>";
        }

        $imgstyleAttr = $imgstyle !== '' ? " style=\"{$imgstyle}\"" : '';

        // Data field value
        if (($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
            if ($this->hasValidDataField()) {
                $this->_imagesource = (string) $this->readDataFieldValue();
            }
        }

        // Custom event
        $this->callEvent('oncustomize', []);

        // Image source
        $source = $this->_imagesource;
        if (($this->ControlState & CS_DESIGNING) !== CS_DESIGNING && $this->_binary && isset($this->owner)) {
            $key = md5($this->owner->Name . $this->Name . $this->Left . $this->Top . $this->Width . $this->Height);
            $url = $_SERVER['PHP_SELF'] ?? '';
            $source = "{$url}?bimg={$key}";
        }

        $sourceEsc = htmlspecialchars($source);
        echo "<img id=\"{$name}\" src=\"{$sourceEsc}\"{$attr}{$imgstyleAttr}{$classAttr}{$hint} {$map} {$events} />";

        if ($this->_link !== '') {
            echo "</a>";
        }

        echo "</div>";

        // Image map for child controls
        if ($this->controls->count() > 0) {
            echo "<map name=\"map{$name}\">\n";
            foreach ($this->controls->items as $control) {
                if ($control->Visible) {
                    $control->show();
                }
            }
            echo "</map>";
        }
    }

    /**
     * Dump form hidden fields.
     */
    public function dumpFormItems(): void
    {
        if ($this->_onclick !== null) {
            $hiddenwrapperfield = $this->readJSWrapperHiddenFieldName();
            echo "<input type=\"hidden\" id=\"{$hiddenwrapperfield}\" name=\"{$hiddenwrapperfield}\" value=\"\" />";
        }
    }

    /**
     * Dump JavaScript for click handling.
     */
    public function dumpJavascript(): void
    {
        parent::dumpJavascript();

        if ($this->_onclick !== null && !defined($this->_onclick)) {
            define($this->_onclick, 1);
            echo $this->getJSWrapperFunction($this->_onclick);
        }
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
    public function getAutosize(): bool { return $this->_autosize; }
    public function setAutosize(bool $value): void { $this->AutoSize = $value; }
    public function defaultAutosize(): int { return 0; }

    public function getBorder(): bool { return $this->_border; }
    public function setBorder(bool $value): void { $this->Border = $value; }
    public function defaultBorder(): int { return 0; }

    public function getBorderColor(): string { return $this->_bordercolor; }
    public function setBorderColor(string $value): void { $this->BorderColor = $value; }
    public function defaultBorderColor(): string { return ''; }

    public function getCenter(): bool { return $this->_center; }
    public function setCenter(bool $value): void { $this->Center = $value; }
    public function defaultCenter(): int { return 0; }

    public function getDataField(): string { return $this->_datafield; }
    public function setDataField(string $value): void { $this->DataField = $value; }
    public function defaultDataField(): string { return ''; }

    public function getDataSource(): mixed { return $this->_datasource; }
    public function setDataSource(mixed $value): void { $this->DataSource = $this->fixupProperty($value); }
    public function defaultDataSource(): string { return ''; }

    public function getImageSource(): string { return $this->_imagesource; }
    public function setImageSource(string $value): void { $this->ImageSource = $value; }
    public function defaultImageSource(): string { return ''; }

    public function getLink(): string { return $this->_link; }
    public function setLink(string $value): void { $this->Link = $value; }
    public function defaultLink(): string { return ''; }

    public function getLinkTarget(): string { return $this->_linktarget; }
    public function setLinkTarget(string $value): void { $this->LinkTarget = $value; }
    public function defaultLinkTarget(): string { return ''; }

    public function getProportional(): bool { return $this->_proportional; }
    public function setProportional(bool $value): void { $this->Proportional = $value; }
    public function defaultProportional(): int { return 0; }

    public function getStretch(): bool { return $this->_stretch; }
    public function setStretch(bool $value): void { $this->Stretch = $value; }
    public function defaultStretch(): int { return 0; }

    public function getBinary(): bool { return $this->_binary; }
    public function setBinary(bool $value): void { $this->Binary = $value; }
    public function defaultBinary(): int { return 0; }

    public function getBinaryType(): string { return $this->_binarytype; }
    public function setBinaryType(string $value): void { $this->BinaryType = $value; }
    public function defaultBinaryType(): string { return 'image/jpeg'; }

    public function getOnClick(): ?string { return $this->_onclick; }
    public function setOnClick(?string $value): void { $this->OnClick = $value; }
    public function defaultOnClick(): ?string { return null; }

    public function getOnCustomize(): ?string { return $this->_oncustomize; }
    public function setOnCustomize(?string $value): void { $this->OnCustomize = $value; }
    public function defaultOnCustomize(): ?string { return null; }
}
