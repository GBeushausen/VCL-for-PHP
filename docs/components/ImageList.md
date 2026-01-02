# ImageList

A component that holds a list of image paths.

**Namespace:** `VCL\Graphics`
**File:** `src/VCL/Graphics/ImageList.php`
**Extends:** `Component`

## Usage

```php
use VCL\Graphics\ImageList;

$imageList = new ImageList($this);
$imageList->Name = 'ImageList1';
$imageList->Images = [
    'home' => '/images/icons/home.png',
    'save' => '/images/icons/save.png',
    'edit' => '/images/icons/edit.png',
    'delete' => '/images/icons/delete.png',
];
```

## Description

Unlike the VCL for Windows TImageList which stores actual bitmap images, this web-oriented version stores paths to image files. It serves as a central repository for image URLs that can be referenced by other components.

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Images` | `array` | `[]` | Array of image paths (key => path) |
| `Count` | `int` | (read-only) | Number of images in the list |

## Methods

| Method | Description |
|--------|-------------|
| `getImage(string\|int $key)` | Get image path by key |
| `getImageByID(string\|int $key, bool $preformat = false)` | Get image with optional JS formatting |
| `addImage(string $path, string\|int\|null $key = null)` | Add an image to the list |
| `removeImage(string\|int $key)` | Remove an image by key |
| `clear()` | Remove all images |
| `hasImage(string\|int $key)` | Check if image exists |
| `getKeys()` | Get all image keys |

## Example: Button Icons

```php
class MyPage extends Page
{
    public ?ImageList $Icons = null;
    public ?Button $SaveButton = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->Name = 'MyPage';

        // Create image list
        $this->Icons = new ImageList($this);
        $this->Icons->Name = 'Icons';
        $this->Icons->Images = [
            'save' => '/images/save.png',
            'cancel' => '/images/cancel.png',
            'delete' => '/images/delete.png',
        ];

        // Use image in a button
        $this->SaveButton = new Button($this);
        $this->SaveButton->Name = 'SaveButton';
        $this->SaveButton->Parent = $this;
        $this->SaveButton->Caption = 'Save';
        // Reference the image list in your button rendering
    }

    public function getButtonIcon(string $name): string
    {
        return $this->Icons->getImage($name) ?? '';
    }
}
```

## Dynamic Image Management

```php
// Add images at runtime
$this->Icons->addImage('/images/new.png', 'new');
$this->Icons->addImage('/images/copy.png', 'copy');

// Add with auto-generated key
$this->Icons->addImage('/images/misc.png');  // Key will be numeric

// Remove an image
$this->Icons->removeImage('delete');

// Check if image exists
if ($this->Icons->hasImage('save')) {
    $iconUrl = $this->Icons->getImage('save');
}

// Get all keys
$allKeys = $this->Icons->getKeys();  // ['save', 'cancel', 'new', 'copy', 0]

// Clear all images
$this->Icons->clear();
```

## JavaScript Integration

```php
// Get image formatted for JavaScript
$jsImage = $this->Icons->getImageByID('save', true);
// Returns: "/images/save.png" or "null" if not found

// Use in JavaScript output
echo '<script>';
echo 'var saveIcon = ' . $this->Icons->getImageByID('save', true) . ';';
echo '</script>';
```

## VCL Path Placeholder

Images can use the `%VCL_HTTP_PATH%` placeholder which gets replaced with the VCL base path:

```php
$this->Icons->Images = [
    'logo' => '%VCL_HTTP_PATH%/images/logo.png',
];

// When retrieved, %VCL_HTTP_PATH% is replaced with the actual path
$logoUrl = $this->Icons->getImageByID('logo');
```

## Using with Other Components

```php
// Example: Creating a toolbar with icons
class Toolbar extends Page
{
    public ?ImageList $ToolbarIcons = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->ToolbarIcons = new ImageList($this);
        $this->ToolbarIcons->Name = 'ToolbarIcons';
        $this->ToolbarIcons->Images = [
            0 => '/icons/new.png',
            1 => '/icons/open.png',
            2 => '/icons/save.png',
            3 => '/icons/print.png',
        ];
    }

    public function renderToolbar(): string
    {
        $html = '<div class="toolbar">';
        foreach ($this->ToolbarIcons->getKeys() as $index) {
            $iconUrl = $this->ToolbarIcons->getImage($index);
            $html .= '<img src="' . htmlspecialchars($iconUrl) . '" alt="Tool ' . $index . '">';
        }
        $html .= '</div>';
        return $html;
    }
}
```

## Notes

- Keys can be strings or integers
- Missing keys return `null` from `getImage()` or `"null"` from `getImageByID()` with preformat
- The component doesn't validate that image paths actually exist
- Use meaningful string keys for better code readability
