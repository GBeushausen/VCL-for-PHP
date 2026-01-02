# MySQLQuery

A dataset component for executing SQL queries against MySQL databases.

**Namespace:** `VCL\Database\MySQL`
**File:** `src/VCL/Database/MySQL/MySQLQuery.php`
**Extends:** `CustomMySQLQuery`

## Usage

```php
use VCL\Database\MySQL\MySQLQuery;
use VCL\Database\MySQL\MySQLDatabase;

$query = new MySQLQuery($this);
$query->Name = "Query1";
$query->Database = $this->DB;
$query->SQL = "SELECT * FROM users";
$query->Active = true;
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Database` | `mixed` | `null` | MySQLDatabase connection |
| `SQL` | `array\|string` | `[]` | SQL statement(s) |
| `Active` | `bool` | `false` | Open/close the query |
| `TableName` | `string` | `''` | Primary table name (for updates) |
| `Filter` | `string` | `''` | WHERE clause filter |
| `OrderField` | `string` | `''` | ORDER BY field |
| `Order` | `string` | `'asc'` | Sort direction (asc/desc) |
| `Params` | `array` | `[]` | Query parameters |

## Dataset Navigation

| Method | Description |
|--------|-------------|
| `Open()` | Execute query and open dataset |
| `Close()` | Close the dataset |
| `First()` | Move to first record |
| `Last()` | Move to last record |
| `Next()` | Move to next record |
| `Prior()` | Move to previous record |
| `MoveBy(int $n)` | Move by n records |
| `EOF` | Check if at end of dataset |
| `BOF` | Check if at beginning of dataset |

## Dataset Modification

| Method | Description |
|--------|-------------|
| `Insert()` | Insert a new record |
| `Edit()` | Edit current record |
| `Delete()` | Delete current record |
| `Post()` | Save pending changes |
| `Cancel()` | Cancel pending changes |
| `Refresh()` | Re-execute query |

## Accessing Field Values

```php
// Direct property access
$username = $this->Query1->username;
$email = $this->Query1->email;

// Or via Fields collection
$username = $this->Query1->Fields['username']->Value;
```

## Example: Simple Query

```php
class MyPage extends Page
{
    public ?MySQLDatabase $DB = null;
    public ?MySQLQuery $UsersQuery = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->DB = new MySQLDatabase($this);
        $this->DB->Name = "DB";
        $this->DB->Host = "localhost";
        $this->DB->DatabaseName = "myapp";
        $this->DB->UserName = "root";
        $this->DB->UserPassword = "";

        $this->UsersQuery = new MySQLQuery($this);
        $this->UsersQuery->Name = "UsersQuery";
        $this->UsersQuery->Database = $this->DB;
        $this->UsersQuery->SQL = "SELECT id, username, email FROM users";
        $this->UsersQuery->Active = true;
    }

    public function showUsers(): void
    {
        $this->UsersQuery->First();
        while (!$this->UsersQuery->EOF) {
            echo $this->UsersQuery->username . "\n";
            $this->UsersQuery->Next();
        }
    }
}
```

## Example: With Filter and Order

```php
$this->Query1 = new MySQLQuery($this);
$this->Query1->Database = $this->DB;
$this->Query1->SQL = "SELECT * FROM products";
$this->Query1->Filter = "category_id = 5 AND price > 10";
$this->Query1->OrderField = "name";
$this->Query1->Order = "asc";
$this->Query1->Active = true;
```

## Example: Insert Record

```php
$this->UsersQuery->Insert();
$this->UsersQuery->username = "newuser";
$this->UsersQuery->email = "newuser@example.com";
$this->UsersQuery->Post();
```

## Example: Update Record

```php
// Find and edit a record
$this->UsersQuery->First();
while (!$this->UsersQuery->EOF) {
    if ($this->UsersQuery->id == $targetId) {
        $this->UsersQuery->Edit();
        $this->UsersQuery->email = "updated@example.com";
        $this->UsersQuery->Post();
        break;
    }
    $this->UsersQuery->Next();
}
```

## Example: Delete Record

```php
$this->UsersQuery->First();
while (!$this->UsersQuery->EOF) {
    if ($this->UsersQuery->id == $deleteId) {
        $this->UsersQuery->Delete();
        break;
    }
    $this->UsersQuery->Next();
}
```

## SQL as Array

SQL can be set as an array of lines for readability:

```php
$this->Query1->SQL = [
    "SELECT u.id, u.username, o.total",
    "FROM users u",
    "LEFT JOIN orders o ON o.user_id = u.id",
    "WHERE u.active = 1"
];
```

## Master-Detail Relationship

```php
// Master query
$this->OrdersQuery = new MySQLQuery($this);
$this->OrdersQuery->Database = $this->DB;
$this->OrdersQuery->SQL = "SELECT * FROM orders";
$this->OrdersQuery->Active = true;

// Detail query
$this->ItemsQuery = new MySQLQuery($this);
$this->ItemsQuery->Database = $this->DB;
$this->ItemsQuery->SQL = "SELECT * FROM order_items";
$this->ItemsQuery->MasterSource = $this->OrdersDataSource;
$this->ItemsQuery->MasterFields = ['order_id' => 'id'];
$this->ItemsQuery->Active = true;
```

## Notes

- Query is re-executed when `Filter`, `OrderField`, or `Order` change
- `TableName` must be set for Insert/Edit/Delete to work
- Use `Prepare()` for queries that will be executed multiple times
- Dataset state is one of: Browse, Edit, Insert, Inactive
