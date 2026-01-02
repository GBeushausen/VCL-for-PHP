<?php

declare(strict_types=1);

namespace VCL\Styles;

/**
 * A class to allow import and use stylesheets.
 *
 * This component allows you to link a StyleSheet file stored in a .css file.
 * Components having Style property will show available styles populated by this component.
 *
 * To use it, drop this component on a form, assign the FileName property to a .css file,
 * and drop any control like Button. After that, drop down Style property of Button
 * to show available styles in the stylesheet.
 *
 * @link http://www.w3.org/Style/CSS/
 *
 * PHP 8.4 version with Property Hooks.
 */
class StyleSheet extends CustomStyleSheet
{
    // All properties are inherited from CustomStyleSheet with Property Hooks

    // Legacy getters/setters (publish properties)
    public function getFileName(): string { return $this->readFileName(); }
    public function setFileName(string $value): void { $this->writeFileName($value); }

    public function getIncludeStandard(): bool { return $this->readIncludeStandard(); }
    public function setIncludeStandard(bool $value): void { $this->writeIncludeStandard($value); }

    public function getIncludeID(): bool { return $this->readIncludeID(); }
    public function setIncludeID(bool $value): void { $this->writeIncludeID($value); }

    public function getIncludeSubStyle(): bool { return $this->readIncludeSubStyle(); }
    public function setIncludeSubStyle(bool $value): void { $this->writeIncludeSubStyle($value); }
}
