<?php

declare(strict_types=1);

namespace VCL\Menus;

use VCL\Core\Component;
use VCL\Security\Escaper;

/**
 * CustomPopupMenu is the base class for PopupMenu.
 *
 * Use PopupMenu to define the pop-up menu that appears when the user clicks on
 * a control with the right mouse button.
 *
 * PHP 8.4 version with Property Hooks.
 */
class CustomPopupMenu extends Component
{
    protected array $_items = [];
    protected ?string $_onclick = null;
    protected ?string $_jsonclick = null;
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

    /**
     * Called when component is loaded.
     */
    public function loaded(): void
    {
        parent::loaded();
        $this->Images = $this->_images;
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
     * Dump form items (hidden fields).
     */
    public function dumpFormItems(): void
    {
        $htmlName = Escaper::attr($this->_name);
        echo "<input type=\"hidden\" id=\"{$htmlName}_state\" name=\"{$htmlName}_state\" value=\"\" />\n";
    }

    /**
     * Dump JavaScript for the menu.
     */
    public function dumpJavascript(): void
    {
        $this->dumpJSEvent($this->_jsonclick);
    }

    /**
     * Dump header code for the popup menu.
     */
    public function dumpHeaderCode(): void
    {
        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            return;
        }

        $this->dumpPopupMenuCSS();

        // Escape names for use as JS identifiers and strings
        $safeName = Escaper::id($this->_name);
        $jsNameString = Escaper::jsString($this->_name);

        echo "<script type=\"text/javascript\">\n";

        // Create menu function
        echo "var {$safeName};\n";
        echo "function {$safeName}CreateMenu() {\n";
        echo "  if (typeof {$safeName} !== 'undefined' && {$safeName} !== null) return;\n";
        echo "  var menu = document.createElement('div');\n";
        echo "  menu.id = '{$jsNameString}_popup';\n";
        echo "  menu.className = 'vcl-popup-menu';\n";
        echo "  menu.style.display = 'none';\n";
        echo "  menu.innerHTML = " . json_encode($this->buildMenuHTML($this->_items)) . ";\n";
        echo "  document.body.appendChild(menu);\n";
        echo "  {$safeName} = menu;\n";

        // Add click handler to hide menu when clicking outside
        echo "  document.addEventListener('click', function(e) {\n";
        echo "    if ({$safeName} && !{$safeName}.contains(e.target)) {\n";
        echo "      {$safeName}.style.display = 'none';\n";
        echo "    }\n";
        echo "  });\n";

        echo "}\n\n";

        // Submit menu event function
        $formName = $this->owner !== null ? Escaper::id($this->owner->Name) : '';

        echo "function {$safeName}SubmitEvent(tag) {\n";
        echo "  var submit = true;\n";

        if ($this->_jsonclick !== null) {
            $safeCallback = Escaper::id($this->_jsonclick);
            echo "  submit = {$safeCallback}({tag: tag});\n";
        }

        echo "  if (tag !== 0 && submit) {\n";
        echo "    var hid = document.getElementById('{$jsNameString}_state');\n";
        echo "    if (hid) hid.value = tag;\n";

        // Use consistent syntax: document.formName or document.forms[0]
        if ($formName !== '') {
            echo "    var form = document['{$formName}'];\n";
        } else {
            echo "    var form = document.forms[0];\n";
        }

        echo "    if (form && form.submit) form.submit();\n";
        echo "  }\n";
        echo "  if ({$safeName}) {$safeName}.style.display = 'none';\n";
        echo "}\n\n";

        // Show menu function
        echo "function Show{$safeName}(event, type) {\n";
        echo "  {$safeName}CreateMenu();\n";
        echo "  if (!{$safeName}) return;\n";
        echo "  var x, y;\n";
        echo "  if (event.pageX !== undefined) {\n";
        echo "    x = event.pageX;\n";
        echo "    y = event.pageY;\n";
        echo "  } else {\n";
        echo "    x = event.clientX + document.body.scrollLeft;\n";
        echo "    y = event.clientY + document.body.scrollTop;\n";
        echo "  }\n";
        echo "  {$safeName}.style.left = x + 'px';\n";
        echo "  {$safeName}.style.top = y + 'px';\n";
        echo "  {$safeName}.style.display = 'block';\n";
        echo "  event.preventDefault();\n";
        echo "  return false;\n";
        echo "}\n";

        echo "</script>\n";
    }

    /**
     * Build the HTML for the popup menu.
     */
    protected function buildMenuHTML(array $items, int $level = 0): string
    {
        $html = '<ul class="vcl-popup-menu-list">';

        // Escape name for use as JS identifier in onclick
        $safeName = Escaper::id($this->_name);

        foreach ($items as $index => $item) {
            $caption = $item['Caption'] ?? '';
            $tag = (int) ($item['Tag'] ?? 0);  // Force integer for tag
            $imageIndex = $item['ImageIndex'] ?? -1;
            $subItems = $item['Items'] ?? [];

            if ($caption === '-') {
                $html .= '<li class="vcl-menu-separator"><hr /></li>';
                continue;
            }

            $hasSubItems = !empty($subItems);
            $itemClass = $hasSubItems ? 'vcl-menu-item has-submenu' : 'vcl-menu-item';

            $html .= '<li class="' . $itemClass . '">';

            $escapedCaption = Escaper::html($caption);
            $image = $this->getItemImage($imageIndex);

            $html .= '<a href="#" onclick="' . $safeName . 'SubmitEvent(' . $tag . '); return false;">';
            if ($image !== '') {
                // Validate image URL before using
                $safeImage = Escaper::urlAttr($image);
                if ($safeImage !== '#') {
                    $html .= '<img src="' . Escaper::attr($safeImage) . '" alt="" class="vcl-menu-icon" />';
                }
            }
            $html .= '<span>' . $escapedCaption . '</span>';
            if ($hasSubItems) {
                $html .= '<span class="vcl-menu-arrow">▸</span>';
            }
            $html .= '</a>';

            if ($hasSubItems) {
                $html .= $this->buildMenuHTML($subItems, $level + 1);
            }

            $html .= '</li>';
        }

        $html .= '</ul>';
        return $html;
    }

    /**
     * Get the image path for a menu item.
     */
    protected function getItemImage(int $imageIndex): string
    {
        if ($imageIndex < 0 || $this->_images === null) {
            return '';
        }

        if (is_object($this->_images) && method_exists($this->_images, 'readImageByID')) {
            $path = $this->_images->readImageByID($imageIndex, 1);
            return $path !== 'null' ? $path : '';
        }

        return '';
    }

    /**
     * Dump CSS for the popup menu.
     */
    protected function dumpPopupMenuCSS(): void
    {
        static $cssDumped = false;
        if ($cssDumped) {
            return;
        }
        $cssDumped = true;

        echo "<style>\n";
        echo ".vcl-popup-menu { position: absolute; z-index: 10000; font-family: sans-serif; font-size: 14px; }\n";
        echo ".vcl-popup-menu-list { list-style: none; margin: 0; padding: 0; background: #fff; border: 1px solid #ccc; min-width: 150px; box-shadow: 2px 2px 5px rgba(0,0,0,0.2); }\n";
        echo ".vcl-popup-menu .vcl-menu-item { position: relative; }\n";
        echo ".vcl-popup-menu .vcl-menu-item > a { display: flex; align-items: center; padding: 6px 12px; text-decoration: none; color: #333; }\n";
        echo ".vcl-popup-menu .vcl-menu-item > a:hover { background: #e8e8e8; }\n";
        echo ".vcl-popup-menu .vcl-menu-icon { width: 16px; height: 16px; margin-right: 8px; }\n";
        echo ".vcl-popup-menu .vcl-menu-arrow { margin-left: auto; padding-left: 10px; }\n";
        echo ".vcl-popup-menu .vcl-menu-separator { padding: 2px 0; }\n";
        echo ".vcl-popup-menu .vcl-menu-separator hr { margin: 0; border: none; border-top: 1px solid #ccc; }\n";
        echo ".vcl-popup-menu .has-submenu > .vcl-popup-menu-list { display: none; position: absolute; left: 100%; top: 0; }\n";
        echo ".vcl-popup-menu .has-submenu:hover > .vcl-popup-menu-list { display: block; }\n";
        echo "</style>\n";
    }

    // Legacy getters/setters
    protected function readItems(): array { return $this->_items; }
    protected function writeItems(array $value): void { $this->Items = $value; }

    protected function readImages(): mixed { return $this->_images; }
    protected function writeImages(mixed $value): void { $this->Images = $value; }
    public function defaultImages(): mixed { return null; }

    protected function readOnClick(): ?string { return $this->_onclick; }
    protected function writeOnClick(?string $value): void { $this->_onclick = $value; }

    protected function readjsOnClick(): ?string { return $this->_jsonclick; }
    protected function writejsOnClick(?string $value): void { $this->_jsonclick = $value; }
}
