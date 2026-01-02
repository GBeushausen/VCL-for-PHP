# Pager

A control for paginating data sets.

**Namespace:** `VCL\ComCtrls`
**File:** `src/VCL/ComCtrls/Pager.php`
**Extends:** `Control`

## Usage

```php
use VCL\ComCtrls\Pager;

$pager = new Pager($this);
$pager->Name = 'Pager1';
$pager->Parent = $this;
$pager->Left = 20;
$pager->Top = 300;
$pager->DesignTotalRecords = 250;
$pager->RecordsPerPage = 20;
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `DesignTotalRecords` | `int` | `100` | Total number of records |
| `RecordsPerPage` | `int` | `10` | Records shown per page |
| `MaxButtons` | `int` | `10` | Max page buttons before showing "..." |
| `CSSFile` | `string` | `'pager.css'` | CSS file for styling |
| `NextCaption` | `string` | `'Next &raquo;'` | Caption for next button |
| `PreviousCaption` | `string` | `'&laquo; Previous'` | Caption for previous button |
| `Datasource` | `Datasource\|null` | `null` | Linked datasource for automatic pagination |
| `CurrentPage` | `int` | `1` | Current page number (1-based) |
| `PageCount` | `int` | (read-only) | Total number of pages |

## Methods

| Method | Description |
|--------|-------------|
| `goToPage(int $page)` | Navigate to specific page |
| `firstPage()` | Go to first page |
| `lastPage()` | Go to last page |
| `nextPage()` | Go to next page |
| `previousPage()` | Go to previous page |
| `getOffset()` | Get current offset for SQL LIMIT |

## Standalone Usage

```php
class ProductListPage extends Page
{
    public ?Pager $Pager1 = null;
    private array $products = [];

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->Name = 'ProductListPage';

        $this->Pager1 = new Pager($this);
        $this->Pager1->Name = 'Pager1';
        $this->Pager1->Parent = $this;
        $this->Pager1->RecordsPerPage = 10;
        $this->Pager1->MaxButtons = 5;
    }

    public function init(): void
    {
        parent::init();

        // Get total count from database
        $totalProducts = $this->getProductCount();
        $this->Pager1->DesignTotalRecords = $totalProducts;

        // Load products for current page
        $offset = $this->Pager1->getOffset();
        $limit = $this->Pager1->RecordsPerPage;
        $this->products = $this->loadProducts($offset, $limit);
    }

    private function getProductCount(): int
    {
        // SELECT COUNT(*) FROM products
        return 250;
    }

    private function loadProducts(int $offset, int $limit): array
    {
        // SELECT * FROM products LIMIT $offset, $limit
        return [];
    }
}
```

## With Datasource

When linked to a Datasource, the Pager automatically manages dataset pagination:

```php
$this->Pager1 = new Pager($this);
$this->Pager1->Name = 'Pager1';
$this->Pager1->Parent = $this;
$this->Pager1->RecordsPerPage = 25;
$this->Pager1->Datasource = $this->Datasource1;

// The pager will:
// 1. Get total record count from the dataset
// 2. Set LimitStart and LimitCount on the dataset
// 3. Automatically update when page changes
```

## Customizing Navigation Text

```php
$this->Pager1->PreviousCaption = '&larr; Back';
$this->Pager1->NextCaption = 'Forward &rarr;';
```

## Custom Styling

The Pager generates HTML with CSS classes for easy styling:

```css
.vcl-pager {
    display: flex;
    gap: 4px;
    align-items: center;
}

.vcl-pager a,
.vcl-pager span {
    padding: 6px 12px;
    text-decoration: none;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.vcl-pager a:hover {
    background-color: #f5f5f5;
}

.vcl-pager .current {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}

.vcl-pager .disabled {
    color: #999;
    cursor: not-allowed;
}
```

## Programmatic Navigation

```php
// Navigate to specific page
$this->Pager1->goToPage(5);

// Navigation shortcuts
$this->Pager1->firstPage();
$this->Pager1->lastPage();
$this->Pager1->nextPage();
$this->Pager1->previousPage();

// Get current state
$currentPage = $this->Pager1->CurrentPage;
$totalPages = $this->Pager1->PageCount;
$offset = $this->Pager1->getOffset();
```

## Window Navigation

When there are many pages, the Pager shows "..." buttons:

```
[<< Previous] [1] [2] [3] [4] [5] [...] [Next >>]
```

The `MaxButtons` property controls how many page numbers show before the "..." appears.

## Session Persistence

The Pager automatically stores and restores the current page in the session, so users return to the same page after form submissions or navigation.

## SQL Integration Example

```php
public function loadData(): array
{
    $offset = $this->Pager1->getOffset();
    $limit = $this->Pager1->RecordsPerPage;

    $sql = "SELECT * FROM products ORDER BY name LIMIT ?, ?";
    // Execute with $offset and $limit parameters

    return $results;
}
```

## Notes

- Page numbers are 1-based (first page is 1, not 0)
- The component generates URL parameters in the format `?Pager1=3`
- Current page is automatically read from URL parameters
- Works with both manual record counts and Datasource-linked datasets
