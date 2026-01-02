# DBPaginator

A control to browse through records of a datasource.

**Namespace:** `VCL\DBCtrls`
**File:** `src/VCL/DBCtrls/DBPaginator.php`
**Extends:** `CustomControl`

## Usage

```php
use VCL\DBCtrls\DBPaginator;

$paginator = new DBPaginator($this);
$paginator->Name = "Paginator1";
$paginator->Parent = $this;
$paginator->DataSource = $this->DataSource1;
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `DataSource` | `mixed` | `null` | Data source component |
| `ShownRecordsCount` | `int` | `10` | Number of page links to show |
| `Orientation` | `string` | `'noHorizontal'` | Layout direction |
| `CaptionFirst` | `string` | `'First'` | First button text |
| `CaptionPrevious` | `string` | `'Prev'` | Previous button text |
| `CaptionNext` | `string` | `'Next'` | Next button text |
| `CaptionLast` | `string` | `'Last'` | Last button text |
| `ShowFirst` | `bool` | `true` | Show First button |
| `ShowPrevious` | `bool` | `true` | Show Previous button |
| `ShowNext` | `bool` | `true` | Show Next button |
| `ShowLast` | `bool` | `true` | Show Last button |
| `PageNumberFormat` | `string` | `'%d'` | Page number format |

## Orientation Values

| Value | Description |
|-------|-------------|
| `noHorizontal` | Horizontal layout |
| `noVertical` | Vertical layout |

## Events

| Property | Type | Description |
|----------|------|-------------|
| `OnClick` | `?string` | Called when page changes |

## Example

```php
$this->Paginator = new DBPaginator($this);
$this->Paginator->Name = "Paginator";
$this->Paginator->Parent = $this;
$this->Paginator->Left = 20;
$this->Paginator->Top = 400;
$this->Paginator->Width = 400;
$this->Paginator->DataSource = $this->UsersDS;
$this->Paginator->ShownRecordsCount = 10;
$this->Paginator->CaptionFirst = "<<";
$this->Paginator->CaptionPrevious = "<";
$this->Paginator->CaptionNext = ">";
$this->Paginator->CaptionLast = ">>";
```

## Methods

| Method | Description |
|--------|-------------|
| `linkClick(string $action)` | Programmatically navigate |

### Action Values

- `'first'` - Go to first record
- `'prev'` - Go to previous record
- `'next'` - Go to next record
- `'last'` - Go to last record
- `'N'` - Go to record number N

## Generated HTML

```html
<nav id="Paginator" class="vcl-paginator" style="width: 400px;">
    <ul class="vcl-paginator-list horizontal">
        <li><a href="#" class="vcl-page-link" onclick="Paginator_navigate('first');">First</a></li>
        <li><a href="#" class="vcl-page-link" onclick="Paginator_navigate('prev');">Prev</a></li>
        <li><a href="#" class="vcl-page-link active" onclick="Paginator_navigate('1');">1</a></li>
        <li><a href="#" class="vcl-page-link" onclick="Paginator_navigate('2');">2</a></li>
        <li><a href="#" class="vcl-page-link" onclick="Paginator_navigate('next');">Next</a></li>
        <li><a href="#" class="vcl-page-link" onclick="Paginator_navigate('last');">Last</a></li>
    </ul>
</nav>
```

## Notes

- Automatically maintains current position in session
- Works with any dataset component
- Current page is highlighted
- Navigation buttons disable at boundaries
