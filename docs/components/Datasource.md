# Datasource

Links data-aware controls to a dataset.

**Namespace:** `VCL\Database`
**File:** `src/VCL/Database/Datasource.php`
**Extends:** `Component`

## Usage

```php
use VCL\Database\Datasource;

$ds = new Datasource($this);
$ds->Name = "DataSource1";
$ds->DataSet = $this->Query1;
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `DataSet` | `mixed` | `null` | The dataset (Query, Table) to link |

## Purpose

Datasource acts as an intermediary between:
- **Datasets** (MySQLQuery, MySQLTable)
- **Data-aware controls** (DBGrid, DBEdit, DBRepeater)

This allows multiple controls to share the same dataset and automatically update when data changes.

## Example

```php
class MyPage extends Page
{
    public ?MySQLDatabase $DB = null;
    public ?MySQLQuery $UsersQuery = null;
    public ?Datasource $UsersDS = null;
    public ?DBGrid $UsersGrid = null;
    public ?DBPaginator $Paginator = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        // Database connection
        $this->DB = new MySQLDatabase($this);
        $this->DB->Host = "localhost";
        $this->DB->DatabaseName = "myapp";
        $this->DB->UserName = "root";

        // Query
        $this->UsersQuery = new MySQLQuery($this);
        $this->UsersQuery->Name = "UsersQuery";
        $this->UsersQuery->Database = $this->DB;
        $this->UsersQuery->SQL = "SELECT * FROM users";
        $this->UsersQuery->Active = true;

        // Datasource links query to controls
        $this->UsersDS = new Datasource($this);
        $this->UsersDS->Name = "UsersDS";
        $this->UsersDS->DataSet = $this->UsersQuery;

        // Grid uses datasource
        $this->UsersGrid = new DBGrid($this);
        $this->UsersGrid->Name = "UsersGrid";
        $this->UsersGrid->Parent = $this;
        $this->UsersGrid->DataSource = $this->UsersDS;

        // Paginator uses same datasource
        $this->Paginator = new DBPaginator($this);
        $this->Paginator->Name = "Paginator";
        $this->Paginator->Parent = $this;
        $this->Paginator->DataSource = $this->UsersDS;
    }
}
```

## Data Flow

```
MySQLDatabase
      │
      ▼
MySQLQuery / MySQLTable
      │
      ▼
  Datasource  ◄─── Links dataset to controls
      │
      ├─────► DBGrid
      ├─────► DBPaginator
      ├─────► DBEdit
      └─────► DBRepeater
```

## Multiple Controls, One Dataset

When multiple controls share a datasource:
- Grid shows records
- Paginator navigates records
- DBEdit fields show/edit current record
- All stay synchronized

## Notes

- Required for data-aware controls
- One Datasource per dataset
- Multiple controls can use same Datasource
- Controls auto-update when dataset changes
