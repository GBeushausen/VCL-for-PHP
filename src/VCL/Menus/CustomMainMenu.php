<?php

declare(strict_types=1);

namespace VCL\Menus;

use VCL\Buttons\QWidget;

/**
 * CustomMainMenu is the base class for MainMenu.
 *
 * MainMenu encapsulates a menu bar and its accompanying drop-down menus for an HTML page.
 * To begin designing a menu, add a main menu to a form, and edit its Items property.
 *
 * PHP 8.4 version with Property Hooks.
 */
class CustomMainMenu extends QWidget
{
    protected array $_items = [];
    protected ?string $_onclick = null;
    protected mixed $_images = null;

    // Property Hooks
    public array $Items {
        get => $this->_items;
        set => $this->_items = $value;
    }

    public mixed $Images {
        get => $this->_images;
        set => $this->_images = $this->fixupProperty($value);
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->_width = 300;
        $this->_height = 24;
    }

    /**
     * Initialize the menu.
     */
    public function init(): void
    {
        parent::init();

        // Handle menu click from form submission
        $stateValue = $_POST[$this->_name . '_state'] ?? '';
        if ($stateValue !== '') {
            $this->callEvent('onclick', ['tag' => $stateValue]);
        }
    }

    /**
     * Called when component is loaded.
     */
    public function loaded(): void
    {
        parent::loaded();
        $this->Images = $this->_images;
    }

    /**
     * Dump the menu contents.
     */
    public function dumpContents(): void
    {
        $style = $this->buildMenuStyle();

        echo "<nav id=\"{$this->_name}\" class=\"vcl-mainmenu\" style=\"{$style}\">\n";
        echo "<ul class=\"vcl-menu-bar\">\n";

        $this->dumpMenuItems($this->_items, 0);

        echo "</ul>\n";
        echo "</nav>\n";

        // Hidden field for event handling
        echo "<input type=\"hidden\" id=\"{$this->_name}_state\" name=\"{$this->_name}_state\" value=\"\" />\n";

        $this->dumpMenuCSS();
        $this->dumpMenuJavaScript();
    }

    /**
     * Dump menu items recursively.
     */
    protected function dumpMenuItems(array $items, int $level): void
    {
        foreach ($items as $index => $item) {
            $caption = $item['Caption'] ?? '';
            $tag = $item['Tag'] ?? 0;
            $imageIndex = $item['ImageIndex'] ?? -1;
            $subItems = $item['Items'] ?? [];

            if ($caption === '-') {
                echo "<li class=\"vcl-menu-separator\"><hr /></li>\n";
                continue;
            }

            $hasSubItems = !empty($subItems);
            $itemClass = $hasSubItems ? 'vcl-menu-item has-submenu' : 'vcl-menu-item';

            echo "<li class=\"{$itemClass}\">\n";

            $escapedCaption = htmlspecialchars($caption);
            $image = $this->getItemImage($imageIndex);

            echo "<a href=\"#\" data-tag=\"{$tag}\" onclick=\"{$this->_name}_click(event, {$tag}); return false;\">";
            if ($image !== '') {
                echo "<img src=\"{$image}\" alt=\"\" class=\"vcl-menu-icon\" />";
            }
            echo "<span>{$escapedCaption}</span>";
            if ($hasSubItems) {
                echo "<span class=\"vcl-menu-arrow\">▸</span>";
            }
            echo "</a>\n";

            if ($hasSubItems) {
                echo "<ul class=\"vcl-submenu\">\n";
                $this->dumpMenuItems($subItems, $level + 1);
                echo "</ul>\n";
            }

            echo "</li>\n";
        }
    }

    /**
     * Get the image path for a menu item.
     */
    protected function getItemImage(int $imageIndex): string
    {
        if ($imageIndex < 0 || $this->_images === null) {
            return '';
        }

        if (is_object($this->_images) && method_exists($this->_images, 'readImage')) {
            $path = $this->_images->readImage($imageIndex);
            return $path !== false ? $path : '';
        }

        return '';
    }

    /**
     * Build the menu style string.
     */
    protected function buildMenuStyle(): string
    {
        $styles = [];

        if ($this->_width > 0) {
            $styles[] = "width: {$this->_width}px";
        }
        if ($this->_height > 0) {
            $styles[] = "height: {$this->_height}px";
        }

        if ($this->_color !== '') {
            $styles[] = "background-color: {$this->_color}";
        }

        if (!$this->_visible) {
            $styles[] = "display: none";
        }

        return implode('; ', $styles);
    }

    /**
     * Dump CSS for the menu.
     */
    protected function dumpMenuCSS(): void
    {
        static $cssDumped = false;
        if ($cssDumped) {
            return;
        }
        $cssDumped = true;

        echo "<style>\n";
        echo ".vcl-mainmenu { font-family: sans-serif; font-size: 14px; }\n";
        echo ".vcl-menu-bar { list-style: none; margin: 0; padding: 0; display: flex; background: #f0f0f0; border: 1px solid #ccc; }\n";
        echo ".vcl-menu-bar > .vcl-menu-item { position: relative; }\n";
        echo ".vcl-menu-bar > .vcl-menu-item > a { display: block; padding: 6px 12px; text-decoration: none; color: #333; }\n";
        echo ".vcl-menu-bar > .vcl-menu-item > a:hover { background: #ddd; }\n";
        echo ".vcl-submenu { display: none; position: absolute; left: 0; top: 100%; list-style: none; margin: 0; padding: 0; background: #fff; border: 1px solid #ccc; min-width: 150px; box-shadow: 2px 2px 5px rgba(0,0,0,0.2); z-index: 1000; }\n";
        echo ".vcl-menu-item:hover > .vcl-submenu { display: block; }\n";
        echo ".vcl-submenu .vcl-submenu { left: 100%; top: 0; }\n";
        echo ".vcl-submenu .vcl-menu-item > a { display: flex; align-items: center; padding: 6px 12px; text-decoration: none; color: #333; }\n";
        echo ".vcl-submenu .vcl-menu-item > a:hover { background: #e8e8e8; }\n";
        echo ".vcl-menu-icon { width: 16px; height: 16px; margin-right: 8px; }\n";
        echo ".vcl-menu-arrow { margin-left: auto; padding-left: 10px; }\n";
        echo ".vcl-menu-separator { padding: 2px 0; }\n";
        echo ".vcl-menu-separator hr { margin: 0; border: none; border-top: 1px solid #ccc; }\n";
        echo "</style>\n";
    }

    /**
     * Dump JavaScript for the menu.
     */
    protected function dumpMenuJavaScript(): void
    {
        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            return;
        }

        $formName = $this->_owner !== null ? $this->_owner->Name : 'document.forms[0]';

        echo "<script type=\"text/javascript\">\n";
        echo "function {$this->_name}_click(event, tag) {\n";
        echo "  event.preventDefault();\n";
        echo "  var submit = true;\n";

        if ($this->_jsonclick !== null) {
            echo "  submit = {$this->_jsonclick}(event);\n";
        }

        echo "  if (tag !== 0 && submit) {\n";
        echo "    var hid = document.getElementById('{$this->_name}_state');\n";
        echo "    if (hid) hid.value = tag;\n";
        echo "    var form = document.{$formName};\n";
        echo "    if (form && form.submit) form.submit();\n";
        echo "  }\n";
        echo "}\n";
        echo "</script>\n";
    }

    // Legacy getters/setters
    public function getItems(): array { return $this->_items; }
    public function setItems(array $value): void { $this->Items = $value; }

    public function getImages(): mixed { return $this->_images; }
    public function setImages(mixed $value): void { $this->Images = $value; }
    public function defaultImages(): mixed { return null; }

    public function readOnClick(): ?string { return $this->_onclick; }
    public function writeOnClick(?string $value): void { $this->_onclick = $value; }
}
