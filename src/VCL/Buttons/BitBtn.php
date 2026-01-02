<?php

declare(strict_types=1);

namespace VCL\Buttons;

use VCL\Buttons\Enums\ButtonLayout;
use VCL\Buttons\Enums\ButtonKind;

/**
 * BitBtn is a push button control that can include a bitmap on its face.
 *
 * Bitmap buttons exhibit the same behavior as button controls. Use them to initiate
 * actions from forms or pages.
 *
 * Bitmap buttons implement properties that specify the bitmap images, along with
 * their appearance and placement on the button.
 *
 * PHP 8.4 version with Property Hooks.
 */
class BitBtn extends QWidget
{
    protected ?string $_onclick = null;
    protected string $_imagesource = '';
    protected string $_imagedisabled = '';
    protected string $_imageclicked = '';
    protected string $_buttonlayout = 'blImageLeft';
    protected string $_kind = 'bkCustom';
    protected string $_buttontype = 'btSubmit';
    protected int $_spacing = 4;
    protected bool $_default = false;
    protected bool $_cancel = false;
    protected ?string $_action = null;

    // Property Hooks
    public ?string $OnClick {
        get => $this->_onclick;
        set => $this->_onclick = $value;
    }

    public string $ImageSource {
        get => $this->_imagesource;
        set => $this->_imagesource = $value;
    }

    public string $ImageDisabled {
        get => $this->_imagedisabled;
        set => $this->_imagedisabled = $value;
    }

    public string $ImageClicked {
        get => $this->_imageclicked;
        set => $this->_imageclicked = $value;
    }

    public string $ButtonLayout {
        get => $this->_buttonlayout;
        set => $this->_buttonlayout = $value;
    }

    public string $Kind {
        get => $this->_kind;
        set => $this->_kind = $value;
    }

    public string $ButtonType {
        get => $this->_buttontype;
        set => $this->_buttontype = $value;
    }

    public int $Spacing {
        get => $this->_spacing;
        set => $this->_spacing = max(0, $value);
    }

    public bool $Default {
        get => $this->_default;
        set => $this->_default = $value;
    }

    public bool $Cancel {
        get => $this->_cancel;
        set => $this->_cancel = $value;
    }

    public ?string $Action {
        get => $this->_action;
        set => $this->_action = $value;
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->_width = 75;
        $this->_height = 25;
    }

    /**
     * Initialize the button.
     */
    public function init(): void
    {
        parent::init();

        // Handle click event from form submission
        $hiddenFieldName = $this->readJSWrapperHiddenFieldName();
        $submitEventValue = $this->input->{$hiddenFieldName} ?? null;

        if (is_object($submitEventValue) && $this->_enabled) {
            $expectedValue = $this->readJSWrapperSubmitEventValue($this->_onclick);
            if ($submitEventValue->asString() === $expectedValue) {
                $this->callEvent('onclick', []);
            }
        }
    }

    /**
     * Get the hidden field name for JavaScript wrapper.
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
     * Get the image source path.
     */
    protected function getImageSourcePath(): string
    {
        // Check for predefined button kind images
        if ($this->_kind !== 'bkCustom') {
            $kindEnum = ButtonKind::tryFrom($this->_kind);
            if ($kindEnum !== null) {
                $predefinedImage = $kindEnum->getImagePath();
                if ($predefinedImage !== '') {
                    return $predefinedImage;
                }
            }
        }

        return $this->_imagesource;
    }

    /**
     * Get the flexbox direction based on button layout.
     */
    protected function getFlexDirection(): string
    {
        return match($this->_buttonlayout) {
            'blImageTop' => 'column',
            'blImageBottom' => 'column-reverse',
            'blImageRight' => 'row-reverse',
            default => 'row',
        };
    }

    /**
     * Dump the button contents.
     */
    public function dumpContents(): void
    {
        $style = $this->buildButtonStyle();
        $attributes = $this->buildButtonAttributes();
        $hint = $this->getHintAttribute();

        $buttonType = match($this->_buttontype) {
            'btSubmit' => 'submit',
            'btReset' => 'reset',
            default => 'button',
        };

        echo "<button type=\"{$buttonType}\" id=\"{$this->_name}\" name=\"{$this->_name}\" {$attributes} style=\"{$style}\"{$hint}>";

        $imageSrc = $this->getImageSourcePath();
        if ($imageSrc !== '') {
            $imageStyle = "margin: 0 {$this->_spacing}px;";
            echo "<img src=\"{$imageSrc}\" alt=\"\" style=\"{$imageStyle}\" />";
        }

        if ($this->_caption !== '') {
            echo "<span>" . htmlspecialchars($this->_caption) . "</span>";
        }

        echo "</button>\n";

        // Dump hidden field for event handling
        if ($this->_onclick !== null) {
            $hiddenFieldName = $this->readJSWrapperHiddenFieldName();
            echo "<input type=\"hidden\" id=\"{$hiddenFieldName}\" name=\"{$hiddenFieldName}\" value=\"\" />\n";
        }

        $this->dumpJavaScript();
    }

    /**
     * Build the button style string.
     */
    protected function buildButtonStyle(): string
    {
        $styles = [];

        $styles[] = "display: inline-flex";
        $styles[] = "align-items: center";
        $styles[] = "justify-content: center";
        $styles[] = "flex-direction: " . $this->getFlexDirection();

        if ($this->_width > 0) {
            $styles[] = "width: {$this->_width}px";
        }
        if ($this->_height > 0) {
            $styles[] = "height: {$this->_height}px";
        }

        if ($this->_color !== '') {
            $styles[] = "background-color: {$this->_color}";
        }

        if ($this->_font !== null) {
            if ($this->_font->Family !== '') {
                $styles[] = "font-family: '{$this->_font->Family}'";
            }
            if ($this->_font->Size > 0) {
                $styles[] = "font-size: {$this->_font->Size}px";
            }
            if ($this->_font->Color !== '') {
                $styles[] = "color: {$this->_font->Color}";
            }
            if ($this->_font->Weight !== '') {
                $styles[] = "font-weight: {$this->_font->Weight}";
            }
        }

        if ($this->_cursor !== '') {
            $styles[] = "cursor: " . strtolower(substr($this->_cursor, 2));
        }

        if (!$this->_visible) {
            $styles[] = "display: none";
        }

        return implode('; ', $styles);
    }

    /**
     * Build the button attributes string.
     */
    protected function buildButtonAttributes(): string
    {
        $attrs = [];

        if (!$this->_enabled) {
            $attrs[] = "disabled";
        }

        if ($this->_taborder >= 0) {
            $attrs[] = "tabindex=\"{$this->_taborder}\"";
        }

        return implode(' ', $attrs);
    }

    /**
     * Dump JavaScript for the button.
     */
    protected function dumpJavaScript(): void
    {
        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            return;
        }

        $js = "<script type=\"text/javascript\">\n";
        $js .= "(function() {\n";
        $js .= "  var btn = document.getElementById('{$this->_name}');\n";
        $js .= "  if (!btn) return;\n";

        // Handle onclick event
        if ($this->_onclick !== null) {
            $hiddenFieldName = $this->readJSWrapperHiddenFieldName();
            $submitValue = $this->readJSWrapperSubmitEventValue($this->_onclick);
            $js .= "  btn.addEventListener('click', function(e) {\n";
            $js .= "    var hiddenField = document.getElementById('{$hiddenFieldName}');\n";
            $js .= "    if (hiddenField) hiddenField.value = '{$submitValue}';\n";
            $js .= "  });\n";
        }

        // Handle image states
        if ($this->_imageclicked !== '' && $this->_imagesource !== '') {
            $js .= "  var img = btn.querySelector('img');\n";
            $js .= "  if (img) {\n";
            $js .= "    var originalSrc = img.src;\n";
            $js .= "    btn.addEventListener('mousedown', function() { img.src = '{$this->_imageclicked}'; });\n";
            $js .= "    btn.addEventListener('mouseup', function() { img.src = originalSrc; });\n";
            $js .= "  }\n";
        }

        // Dump common JS events
        $js .= $this->dumpCommonJSEvents('btn');

        $js .= "})();\n";
        $js .= "</script>\n";

        echo $js;
    }

    // Legacy getters/setters
    public function getOnClick(): ?string { return $this->_onclick; }
    public function setOnClick(?string $value): void { $this->OnClick = $value; }
    public function defaultOnClick(): ?string { return null; }

    public function getImageSource(): string { return $this->_imagesource; }
    public function setImageSource(string $value): void { $this->ImageSource = $value; }
    public function defaultImageSource(): string { return ''; }

    public function getImageDisabled(): string { return $this->_imagedisabled; }
    public function setImageDisabled(string $value): void { $this->ImageDisabled = $value; }
    public function defaultImageDisabled(): string { return ''; }

    public function getImageClicked(): string { return $this->_imageclicked; }
    public function setImageClicked(string $value): void { $this->ImageClicked = $value; }
    public function defaultImageClicked(): string { return ''; }

    public function getButtonLayout(): string { return $this->_buttonlayout; }
    public function setButtonLayout(string $value): void { $this->ButtonLayout = $value; }
    public function defaultButtonLayout(): string { return 'blImageLeft'; }

    public function getKind(): string { return $this->_kind; }
    public function setKind(string $value): void { $this->Kind = $value; }
    public function defaultKind(): string { return 'bkCustom'; }

    public function getButtonType(): string { return $this->_buttontype; }
    public function setButtonType(string $value): void { $this->ButtonType = $value; }
    public function defaultButtonType(): string { return 'btSubmit'; }

    public function getSpacing(): int { return $this->_spacing; }
    public function setSpacing(int $value): void { $this->Spacing = $value; }
    public function defaultSpacing(): int { return 4; }

    public function getAction(): ?string { return $this->_action; }
    public function setAction(?string $value): void { $this->Action = $value; }
    public function defaultAction(): ?string { return null; }
}
