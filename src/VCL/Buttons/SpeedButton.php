<?php

declare(strict_types=1);

namespace VCL\Buttons;

/**
 * SpeedButton is a button that is used to execute commands or set modes.
 *
 * Use SpeedButton to add a button to a group of buttons in a form. SpeedButton
 * also introduces properties that allow speed buttons to work together as a group.
 * Speed buttons are commonly grouped in panels to create specialized tool bars
 * and tool palettes.
 *
 * PHP 8.4 version with Property Hooks.
 */
class SpeedButton extends BitBtn
{
    protected bool $_allowallup = false;
    protected bool $_down = false;
    protected bool $_flat = false;
    protected int $_groupindex = 0;

    // Property Hooks
    public bool $AllowAllUp {
        get => $this->_allowallup;
        set => $this->_allowallup = $value;
    }

    public bool $Down {
        get => $this->_down;
        set => $this->_down = $value;
    }

    public bool $Flat {
        get => $this->_flat;
        set => $this->_flat = $value;
    }

    public int $GroupIndex {
        get => $this->_groupindex;
        set => $this->_groupindex = max(0, $value);
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->_width = 25;
        $this->_height = 25;
    }

    /**
     * Called when component is loaded.
     */
    public function loaded(): void
    {
        parent::loaded();

        // Restore down state from form submission
        $submittedDown = $this->input->{$this->_name . 'Down'} ?? null;
        if (is_object($submittedDown) && $submittedDown->asString() !== '') {
            $this->_down = ($submittedDown->asString() === '1');
        }
    }

    /**
     * Dump the button contents.
     */
    public function dumpContents(): void
    {
        // Hidden field for down state
        $downValue = $this->_down ? '1' : '0';
        echo "<input type=\"hidden\" name=\"{$this->_name}Down\" id=\"{$this->_name}Down\" value=\"{$downValue}\" />\n";

        $style = $this->buildSpeedButtonStyle();
        $attributes = $this->buildButtonAttributes();
        $hint = $this->getHintAttribute();

        $buttonType = 'button'; // SpeedButton is always a regular button

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

        $this->dumpSpeedButtonJavaScript();
    }

    /**
     * Build the speed button style string.
     */
    protected function buildSpeedButtonStyle(): string
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
        } else {
            $styles[] = "background-color: buttonface";
        }

        // Flat style
        if ($this->_flat) {
            $styles[] = "border: 1px solid transparent";
        } else {
            $styles[] = "border: 2px outset";
        }

        // Down state
        if ($this->_down) {
            $styles[] = "border-style: inset";
            $styles[] = "background-color: #d0d0d0";
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
        }

        if ($this->_cursor !== '') {
            $styles[] = "cursor: " . strtolower(substr($this->_cursor, 2));
        } else {
            $styles[] = "cursor: pointer";
        }

        if (!$this->_visible) {
            $styles[] = "display: none";
        }

        return implode('; ', $styles);
    }

    /**
     * Dump JavaScript for the speed button.
     */
    protected function dumpSpeedButtonJavaScript(): void
    {
        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            return;
        }

        $groupIndex = $this->_groupindex;
        $allowAllUp = $this->_allowallup ? 'true' : 'false';
        $flat = $this->_flat ? 'true' : 'false';

        $js = "<script type=\"text/javascript\">\n";
        $js .= "(function() {\n";
        $js .= "  var btn = document.getElementById('{$this->_name}');\n";
        $js .= "  var hiddenDown = document.getElementById('{$this->_name}Down');\n";
        $js .= "  if (!btn) return;\n";

        // Group management
        if ($groupIndex > 0) {
            $js .= "  window.speedButtonGroups = window.speedButtonGroups || {};\n";
            $js .= "  window.speedButtonGroups[{$groupIndex}] = window.speedButtonGroups[{$groupIndex}] || [];\n";
            $js .= "  window.speedButtonGroups[{$groupIndex}].push(btn);\n";
        }

        // Click handler for toggle behavior
        $js .= "  btn.addEventListener('click', function(e) {\n";

        if ($groupIndex > 0) {
            $js .= "    var group = window.speedButtonGroups[{$groupIndex}];\n";
            $js .= "    var isDown = hiddenDown.value === '1';\n";
            $js .= "    if (isDown && !{$allowAllUp}) return;\n"; // Can't uncheck if allowAllUp is false
            $js .= "    // Uncheck others in group\n";
            $js .= "    group.forEach(function(b) {\n";
            $js .= "      if (b !== btn) {\n";
            $js .= "        var h = document.getElementById(b.id + 'Down');\n";
            $js .= "        if (h) { h.value = '0'; b.style.borderStyle = {$flat} ? 'solid' : 'outset'; b.style.backgroundColor = ''; }\n";
            $js .= "      }\n";
            $js .= "    });\n";
            $js .= "    // Toggle this button\n";
            $js .= "    hiddenDown.value = isDown ? '0' : '1';\n";
            $js .= "    btn.style.borderStyle = hiddenDown.value === '1' ? 'inset' : ({$flat} ? 'solid' : 'outset');\n";
            $js .= "    btn.style.backgroundColor = hiddenDown.value === '1' ? '#d0d0d0' : '';\n";
        } else {
            // No group - just toggle
            $js .= "    var isDown = hiddenDown.value === '1';\n";
            $js .= "    hiddenDown.value = isDown ? '0' : '1';\n";
            $js .= "    btn.style.borderStyle = hiddenDown.value === '1' ? 'inset' : ({$flat} ? 'solid' : 'outset');\n";
            $js .= "    btn.style.backgroundColor = hiddenDown.value === '1' ? '#d0d0d0' : '';\n";
        }

        $js .= "  });\n";

        // Handle onclick event
        if ($this->_onclick !== null) {
            $hiddenFieldName = $this->readJSWrapperHiddenFieldName();
            $submitValue = $this->readJSWrapperSubmitEventValue($this->_onclick);
            $js .= "  btn.addEventListener('click', function(e) {\n";
            $js .= "    var hiddenField = document.getElementById('{$hiddenFieldName}');\n";
            $js .= "    if (hiddenField) hiddenField.value = '{$submitValue}';\n";
            $js .= "  });\n";
        }

        // Flat button hover effect
        if ($this->_flat) {
            $js .= "  btn.addEventListener('mouseenter', function() {\n";
            $js .= "    if (hiddenDown.value !== '1') btn.style.borderStyle = 'outset';\n";
            $js .= "  });\n";
            $js .= "  btn.addEventListener('mouseleave', function() {\n";
            $js .= "    if (hiddenDown.value !== '1') btn.style.borderStyle = 'solid';\n";
            $js .= "  });\n";
        }

        // Dump common JS events
        $js .= $this->dumpCommonJSEvents('btn');

        $js .= "})();\n";
        $js .= "</script>\n";

        echo $js;
    }

    // Legacy getters/setters
    public function getAllowAllUp(): bool { return $this->_allowallup; }
    public function setAllowAllUp(bool $value): void { $this->AllowAllUp = $value; }
    public function defaultAllowAllUp(): bool { return false; }

    public function getDown(): bool { return $this->_down; }
    public function setDown(bool $value): void { $this->Down = $value; }
    public function defaultDown(): bool { return false; }

    public function getFlat(): bool { return $this->_flat; }
    public function setFlat(bool $value): void { $this->Flat = $value; }
    public function defaultFlat(): bool { return false; }

    public function getGroupIndex(): int { return $this->_groupindex; }
    public function setGroupIndex(int $value): void { $this->GroupIndex = $value; }
    public function defaultGroupIndex(): int { return 0; }
}
