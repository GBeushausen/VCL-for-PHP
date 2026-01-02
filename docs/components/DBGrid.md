# DBGrid

Displays and manipulates records from a dataset in a tabular grid.

**Namespace:** `VCL\DBGrids`
**File:** `src/VCL/DBGrids/DBGrid.php`
**Extends:** `CustomDBGrid`

## Usage

```php
use VCL\DBGrids\DBGrid;

$grid = new DBGrid($this);
$grid->Name = "Grid1";
$grid->Parent = $this;
$grid->DataSource = $this->DataSource1;
$grid->Width = 600;
$grid->Height = 400;
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `DataSource` | `mixed` | `null` | Data source component |
| `Columns` | `array` | `[]` | Column definitions |
| `ReadOnly` | `bool` | `false` | Prevent editing |
| `ShowHeader` | `bool` | `true` | Show column headers |
| `Striped` | `bool` | `true` | Alternate row colors |
| `Hoverable` | `bool` | `true` | Highlight on hover |
| `Bordered` | `bool` | `true` | Show cell borders |
| `SelectedRow` | `int` | `-1` | Currently selected row |
| `FixedColumns` | `int` | `0` | Non-scrolling columns |
| `HeaderClass` | `string` | `''` | CSS class for header |
| `RowClass` | `string` | `''` | CSS class for rows |
| `AlternateRowClass` | `string` | `''` | CSS class for alternate rows |
| `SelectedRowClass` | `string` | `''` | CSS class for selected row |

## Column Properties

```php
$grid->Columns = [
    [
        'Fieldname' => 'id',
        'Caption' => 'ID',
        'Width' => 50,
        'ReadOnly' => true,
        'Alignment' => 'taRightJustify',
        'Color' => '',
        'FontColor' => '',
        'SortType' => 'stNumeric'
    ],
    [
        'Fieldname' => 'name',
        'Caption' => 'Name',
        'Width' => 200,
        'Alignment' => 'taLeftJustify'
    ]
];
```

## Alignment Values

| Value | Description |
|-------|-------------|
| `taLeftJustify` | Left align |
| `taRightJustify` | Right align |
| `taCenter` | Center |

## Events

| Property | Type | Description |
|----------|------|-------------|
| `OnClick` | `?string` | Grid click handler |
| `OnDblClick` | `?string` | Grid double-click handler |
| `jsOnDataChanged` | `?string` | JS function when data changes |
| `jsOnRowChanged` | `?string` | JS function when row selection changes |
| `jsOnRowSaved` | `?string` | JS function when row is saved |

## Example

```php
class MyPage extends Page
{
    public ?DBGrid $UsersGrid = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->UsersGrid = new DBGrid($this);
        $this->UsersGrid->Name = "UsersGrid";
        $this->UsersGrid->Parent = $this;
        $this->UsersGrid->Left = 20;
        $this->UsersGrid->Top = 20;
        $this->UsersGrid->Width = 600;
        $this->UsersGrid->Height = 400;
        $this->UsersGrid->DataSource = $this->UsersDS;
        $this->UsersGrid->Striped = true;
        $this->UsersGrid->Hoverable = true;
        $this->UsersGrid->Columns = [
            ['Fieldname' => 'id', 'Caption' => 'ID', 'Width' => 50, 'ReadOnly' => true],
            ['Fieldname' => 'username', 'Caption' => 'Username', 'Width' => 150],
            ['Fieldname' => 'email', 'Caption' => 'Email', 'Width' => 250],
            ['Fieldname' => 'created_at', 'Caption' => 'Created', 'Width' => 150, 'ReadOnly' => true]
        ];
    }
}
```

## Methods

| Method | Description |
|--------|-------------|
| `getSelectedRowData()` | Get data of selected row |

## Reading Selected Row

```php
public function EditClick(object $sender, array $params): void
{
    $rowData = $this->UsersGrid->getSelectedRowData();
    if ($rowData !== null) {
        $userId = $rowData['id'];
        // Edit user...
    }
}
```

## Generated HTML

```html
<div id="UsersGrid" class="vcl-dbgrid" style="width: 600px; max-height: 400px;">
    <table class="vcl-dbgrid-table striped hoverable bordered">
        <thead>
            <tr><th>ID</th><th>Username</th><th>Email</th></tr>
        </thead>
        <tbody>
            <tr data-row="0" onclick="UsersGrid_selectRow(0)">
                <td>1</td>
                <td><input type="text" name="UsersGrid[0][username]" value="john" /></td>
                <td><input type="text" name="UsersGrid[0][email]" value="john@example.com" /></td>
            </tr>
            <!-- more rows -->
        </tbody>
    </table>
</div>
```

## Notes

- Auto-generates columns from dataset if Columns not specified
- ReadOnly columns display as text, editable as input fields
- Row selection is maintained via hidden field
- CSS classes allow custom styling
