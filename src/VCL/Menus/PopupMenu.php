<?php

declare(strict_types=1);

namespace VCL\Menus;

/**
 * PopupMenu defines the pop-up menu that appears when the user clicks on
 * a control with the right mouse button.
 *
 * To make a pop-up menu available, assign the PopupMenu object to the control's
 * PopupMenu property.
 *
 * PHP 8.4 version with Property Hooks.
 */
class PopupMenu extends CustomPopupMenu
{
    // Property Hooks for Events
    public ?string $OnClick {
        get => $this->_onclick;
        set => $this->_onclick = $value;
    }

    public ?string $jsOnClick {
        get => $this->_jsonclick;
        set => $this->_jsonclick = $value;
    }

    // Legacy getters/setters for published properties
    public function getImages(): mixed { return $this->readImages(); }
    public function setImages(mixed $value): void { $this->writeImages($value); }

    public function getItems(): array { return $this->readItems(); }
    public function setItems(array $value): void { $this->writeItems($value); }

    public function getOnClick(): ?string { return $this->readOnClick(); }
    public function setOnClick(?string $value): void { $this->writeOnClick($value); }

    public function getjsOnClick(): ?string { return $this->readjsOnClick(); }
    public function setjsOnClick(?string $value): void { $this->writejsOnClick($value); }
}
