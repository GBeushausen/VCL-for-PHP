<?php

declare(strict_types=1);

namespace VCL\Menus;

/**
 * MainMenu encapsulates a menu bar and its accompanying drop-down menus for an HTML page.
 *
 * To begin designing a menu, add a main menu to a form, and edit its Items property
 * in the property editor, you can create a complete structure for all the options you want to show.
 *
 * PHP 8.4 version with Property Hooks.
 */
class MainMenu extends CustomMainMenu
{
    // Property Hooks for Events
    public ?string $OnClick {
        get => $this->_onclick;
        set => $this->_onclick = $value;
    }

    // Legacy getters/setters for published properties
    public function getAlignment(): \VCL\UI\Enums\Anchors|string { return $this->_alignment; }
    public function setAlignment(\VCL\UI\Enums\Anchors|string $value): void { $this->Alignment = $value; }

    public function getVisible(): bool { return $this->_visible; }
    public function setVisible(bool $value): void { $this->Visible = $value; }

    public function getOnClick(): ?string { return $this->_onclick; }
    public function setOnClick(?string $value): void { $this->_onclick = $value; }

    public function getjsOnClick(): ?string { return $this->_jsonclick; }
    public function setjsOnClick(?string $value): void { $this->_jsonclick = $value; }
}
