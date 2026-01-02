# DBRepeater

Repeats its child controls for each record in a dataset.

**Namespace:** `VCL\DBCtrls`
**File:** `src/VCL/DBCtrls/DBRepeater.php`
**Extends:** `Panel`

## Usage

```php
use VCL\DBCtrls\DBRepeater;

$repeater = new DBRepeater($this);
$repeater->Name = "Repeater1";
$repeater->Parent = $this;
$repeater->DataSource = $this->DataSource1;
$repeater->Kind = "rkVertical";
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `DataSource` | `mixed` | `null` | Data source component |
| `Kind` | `string` | `'rkVertical'` | Layout direction |
| `RestartDataset` | `bool` | `true` | Start from first record |
| `Limit` | `int` | `0` | Max records (0 = unlimited) |

## Kind Values

| Value | Description |
|-------|-------------|
| `rkVertical` | Stack items vertically (one per row) |
| `rkHorizontal` | Stack items horizontally (all in one row) |

## Example

```php
class MyPage extends Page
{
    public ?MySQLQuery $Query1 = null;
    public ?Datasource $DS1 = null;
    public ?DBRepeater $Repeater1 = null;
    public ?Label $NameLabel = null;
    public ?Label $EmailLabel = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        // Setup query
        $this->Query1 = new MySQLQuery($this);
        $this->Query1->Database = $this->DB;
        $this->Query1->SQL = "SELECT name, email FROM users";
        $this->Query1->Active = true;

        // Setup datasource
        $this->DS1 = new Datasource($this);
        $this->DS1->DataSet = $this->Query1;

        // Setup repeater
        $this->Repeater1 = new DBRepeater($this);
        $this->Repeater1->Name = "Repeater1";
        $this->Repeater1->Parent = $this;
        $this->Repeater1->DataSource = $this->DS1;
        $this->Repeater1->Kind = "rkVertical";
        $this->Repeater1->Limit = 10;

        // Child controls (will be repeated)
        $this->NameLabel = new Label($this);
        $this->NameLabel->Name = "NameLabel";
        $this->NameLabel->Parent = $this->Repeater1;
        $this->NameLabel->DataSource = $this->DS1;
        $this->NameLabel->DataField = "name";

        $this->EmailLabel = new Label($this);
        $this->EmailLabel->Name = "EmailLabel";
        $this->EmailLabel->Parent = $this->Repeater1;
        $this->EmailLabel->DataSource = $this->DS1;
        $this->EmailLabel->DataField = "email";
    }
}
```

## Generated HTML

```html
<table id="Repeater1_table_detail" width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <!-- First record's controls -->
        </td>
    </tr>
    <tr>
        <td>
            <!-- Second record's controls -->
        </td>
    </tr>
    <!-- ... more rows -->
</table>
```

## Notes

- Child controls are rendered for each record
- Data-bound child controls automatically show current record values
- Use Limit to restrict number of displayed records
- Set RestartDataset=false to continue from current position
