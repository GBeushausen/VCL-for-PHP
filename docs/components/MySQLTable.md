# MySQLTable

Encapsulates a database table in a MySQL server.

**Namespace:** `VCL\Database\MySQL`
**File:** `src/VCL/Database/MySQL/MySQLTable.php`
**Extends:** `CustomMySQLTable`

## Usage

```php
use VCL\Database\MySQL\MySQLTable;

$table = new MySQLTable($this);
$table->Name = "Table1";
$table->Database = $this->DB;
$table->TableName = "users";
$table->Active = true;
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Database` | `mixed` | `null` | MySQLDatabase connection |
| `TableName` | `string` | `''` | Database table name |
| `Active` | `bool` | `false` | Open/close the table |
| `Filter` | `string` | `''` | WHERE clause filter |
| `OrderField` | `string` | `''` | ORDER BY field |
| `Order` | `string` | `'asc'` | Sort direction |
| `MasterSource` | `mixed` | `null` | Master datasource for detail |
| `MasterFields` | `array` | `[]` | Field mapping for master-detail |

## Events

| Property | Type | Description |
|----------|------|-------------|
| `OnBeforeOpen` | `?string` | Before table opens |
| `OnAfterOpen` | `?string` | After table opens |
| `OnBeforeClose` | `?string` | Before table closes |
| `OnAfterClose` | `?string` | After table closes |
| `OnBeforeInsert` | `?string` | Before insert mode |
| `OnAfterInsert` | `?string` | After insert mode |
| `OnBeforeEdit` | `?string` | Before edit mode |
| `OnAfterEdit` | `?string` | After edit mode |
| `OnBeforePost` | `?string` | Before saving changes |
| `OnAfterPost` | `?string` | After saving changes |
| `OnBeforeDelete` | `?string` | Before deleting record |
| `OnAfterDelete` | `?string` | After deleting record |
| `OnDeleteError` | `?string` | On delete error |

## Methods

| Method | Description |
|--------|-------------|
| `Open()` | Open the table |
| `Close()` | Close the table |
| `First()` | Move to first record |
| `Last()` | Move to last record |
| `Next()` | Move to next record |
| `Prior()` | Move to previous record |
| `Insert()` | Enter insert mode |
| `Edit()` | Enter edit mode |
| `Delete()` | Delete current record |
| `Post()` | Save pending changes |
| `Cancel()` | Cancel pending changes |
| `EOF()` | Check if at end |
| `BOF()` | Check if at beginning |

## Example

```php
class MyPage extends Page
{
    public ?MySQLDatabase $DB = null;
    public ?MySQLTable $UsersTable = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->DB = new MySQLDatabase($this);
        $this->DB->Host = "localhost";
        $this->DB->DatabaseName = "myapp";
        $this->DB->UserName = "root";

        $this->UsersTable = new MySQLTable($this);
        $this->UsersTable->Name = "UsersTable";
        $this->UsersTable->Database = $this->DB;
        $this->UsersTable->TableName = "users";
        $this->UsersTable->Filter = "active = 1";
        $this->UsersTable->OrderField = "username";
        $this->UsersTable->Active = true;
    }

    public function listUsers(): void
    {
        $this->UsersTable->First();
        while (!$this->UsersTable->EOF()) {
            echo $this->UsersTable->username . "\n";
            $this->UsersTable->Next();
        }
    }

    public function addUser(string $username, string $email): void
    {
        $this->UsersTable->Insert();
        $this->UsersTable->username = $username;
        $this->UsersTable->email = $email;
        $this->UsersTable->Post();
    }
}
```

## MySQLTable vs MySQLQuery

| Feature | MySQLTable | MySQLQuery |
|---------|------------|------------|
| SQL control | Automatic | Custom SQL |
| CRUD | Built-in | Manual setup |
| Joins | No | Yes |
| Best for | Simple tables | Complex queries |

## Notes

- Provides direct access to a single table
- Automatically generates SELECT, INSERT, UPDATE, DELETE
- Use Filter for WHERE conditions
- Use OrderField/Order for sorting
- For complex queries or joins, use MySQLQuery instead
