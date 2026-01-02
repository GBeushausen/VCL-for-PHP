<?php

declare(strict_types=1);

namespace VCL\StdCtrls;

/**
 * Label displays text that the user cannot edit directly.
 *
 * Use Label to add text to a form. Labels are typically used to identify
 * other controls or display status information.
 *
 * PHP 8.4 version with Property Hooks.
 */
class Label extends CustomLabel
{
    // Label publishes all properties from CustomLabel
    // Additional published properties and methods can be added here

    // Legacy getters/setters for published properties
    public function getCaption(): string { return $this->Caption; }
    public function setCaption(string $value): void { $this->Caption = $value; }

    public function getFont(): \VCL\Graphics\Font { return $this->Font; }
    public function setFont(\VCL\Graphics\Font $value): void { $this->Font = $value; }

    public function getAlignment(): \VCL\UI\Enums\Anchors|string { return $this->Alignment; }
    public function setAlignment(\VCL\UI\Enums\Anchors|string $value): void { $this->Alignment = $value; }
    public function defaultAlignment(): string { return 'agNone'; }

    public function getOnClick(): ?string { return $this->_onclick; }
    public function setOnClick(?string $value): void { $this->OnClick = $value; }
    public function defaultOnClick(): ?string { return null; }

    public function getOnDblClick(): ?string { return $this->_ondblclick; }
    public function setOnDblClick(?string $value): void { $this->OnDblClick = $value; }
    public function defaultOnDblClick(): ?string { return null; }
}
