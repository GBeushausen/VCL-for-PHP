# Image

Displays a graphical image.

**Namespace:** `VCL\ExtCtrls`
**File:** `src/VCL/ExtCtrls/Image.php`
**Extends:** `FocusControl`

## Usage

```php
use VCL\ExtCtrls\Image;

$image = new Image($this);
$image->Name = "Image1";
$image->Parent = $this;
$image->Left = 20;
$image->Top = 20;
$image->Width = 200;
$image->Height = 150;
$image->ImageSource = "images/photo.jpg";
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `ImageSource` | `string` | `''` | Path or URL to the image |
| `Left` | `int` | `0` | X position |
| `Top` | `int` | `0` | Y position |
| `Width` | `int` | varies | Width in pixels |
| `Height` | `int` | varies | Height in pixels |
| `AutoSize` | `bool` | `false` | Auto-size to image dimensions |
| `Stretch` | `bool` | `false` | Stretch image to fit dimensions |
| `Proportional` | `bool` | `false` | Maintain aspect ratio when stretching |
| `Center` | `bool` | `false` | Center image in container |
| `Border` | `bool` | `false` | Show border |
| `BorderColor` | `string` | `''` | Border color |
| `Link` | `string` | `''` | URL to navigate when clicked |
| `LinkTarget` | `string` | `''` | Link target (`_blank`, `_self`, etc.) |
| `Visible` | `bool` | `true` | Show/hide |
| `Enabled` | `bool` | `true` | Enable/disable |

### Data-Bound Properties

| Property | Type | Description |
|----------|------|-------------|
| `DataSource` | `mixed` | Data source component |
| `DataField` | `string` | Field name containing image data |
| `Binary` | `bool` | Image data is binary |
| `BinaryType` | `string` | MIME type for binary data (default: `image/jpeg`) |

## Events

| Property | Type | Description |
|----------|------|-------------|
| `OnClick` | `?string` | Click handler method name |

## Examples

### Static Image

```php
$image = new Image($this);
$image->Name = "Logo";
$image->Parent = $this;
$image->Left = 20;
$image->Top = 20;
$image->ImageSource = "images/logo.png";
```

### Stretched Image

```php
$image = new Image($this);
$image->Name = "Banner";
$image->Parent = $this;
$image->Width = 800;
$image->Height = 200;
$image->ImageSource = "images/banner.jpg";
$image->Stretch = true;
$image->Proportional = true;
```

### Clickable Image Link

```php
$image = new Image($this);
$image->Name = "Thumbnail";
$image->Parent = $this;
$image->ImageSource = "images/thumb.jpg";
$image->Link = "https://example.com";
$image->LinkTarget = "_blank";
```

## Generated HTML

```html
<img id="Image1" src="images/photo.jpg"
     style="width: 200px; height: 150px; ..." />
```

With link:
```html
<a href="https://example.com" target="_blank">
    <img id="Image1" src="images/photo.jpg" ... />
</a>
```
