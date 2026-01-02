# MySQLDatabase

Provides a connection to MySQL databases using mysqli.

**Namespace:** `VCL\Database\MySQL`
**File:** `src/VCL/Database/MySQL/MySQLDatabase.php`
**Extends:** `CustomConnection`

## Usage

```php
use VCL\Database\MySQL\MySQLDatabase;

$db = new MySQLDatabase($this);
$db->Name = "Database1";
$db->Host = "localhost";
$db->DatabaseName = "myapp";
$db->UserName = "root";
$db->UserPassword = "password";
$db->Connected = true;
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Host` | `string` | `''` | MySQL server hostname |
| `DatabaseName` | `string` | `''` | Database name |
| `UserName` | `string` | `''` | Username for authentication |
| `UserPassword` | `string` | `''` | Password for authentication |
| `Port` | `int` | `3306` | MySQL port |
| `Charset` | `string` | `'utf8mb4'` | Connection character set |
| `Connected` | `bool` | `false` | Connection state |
| `Debug` | `bool` | `false` | Enable debug logging |
| `Dictionary` | `string` | `''` | Field dictionary table name |

## Methods

### Connection

| Method | Description |
|--------|-------------|
| `Open()` | Open the database connection |
| `Close()` | Close the database connection |

### Query Execution

| Method | Description |
|--------|-------------|
| `Execute(string $query)` | Execute a SQL query |
| `ExecuteLimit(string $query, int $numrows, int $offset)` | Execute with LIMIT |

### Transaction Control

| Method | Description |
|--------|-------------|
| `BeginTrans()` | Start a transaction |
| `CompleteTrans(bool $commit = true)` | Commit or rollback transaction |

### Metadata

| Method | Description |
|--------|-------------|
| `databases()` | Get list of all databases |
| `tables()` | Get list of tables in current database |
| `MetaFields(string $tablename)` | Get field names for a table |
| `extractIndexes(string $table, bool $primary)` | Get table indexes |

### Utilities

| Method | Description |
|--------|-------------|
| `QuoteStr(string $input)` | Escape and quote a string |
| `DBDate(string $input)` | Format a date for MySQL |
| `lastInsertId()` | Get last auto-increment ID |
| `affectedRows()` | Get affected rows from last query |

## Example: Basic Connection

```php
class MyPage extends Page
{
    public ?MySQLDatabase $DB = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->DB = new MySQLDatabase($this);
        $this->DB->Name = "DB";
        $this->DB->Host = "localhost";
        $this->DB->DatabaseName = "myapp";
        $this->DB->UserName = "dbuser";
        $this->DB->UserPassword = "secret";
        $this->DB->Charset = "utf8mb4";
        $this->DB->Connected = true;
    }
}
```

## Example: Execute Query

```php
// Direct query execution
$result = $this->DB->Execute("SELECT * FROM users WHERE active = 1");

while ($row = mysqli_fetch_assoc($result)) {
    echo $row['username'];
}

// With parameters (manual escaping)
$username = $this->DB->QuoteStr($_POST['username']);
$result = $this->DB->Execute("SELECT * FROM users WHERE username = {$username}");
```

## Example: Transaction

```php
try {
    $this->DB->BeginTrans();

    $this->DB->Execute("INSERT INTO orders (customer_id, total) VALUES (1, 99.99)");
    $orderId = $this->DB->lastInsertId();

    $this->DB->Execute("INSERT INTO order_items (order_id, product_id) VALUES ({$orderId}, 5)");

    $this->DB->CompleteTrans(true);  // Commit
} catch (EDatabaseError $e) {
    $this->DB->CompleteTrans(false);  // Rollback
    throw $e;
}
```

## Example: Metadata

```php
// List all tables
$tables = $this->DB->tables();
foreach ($tables as $table) {
    echo "Table: {$table}\n";

    // List fields
    $fields = $this->DB->MetaFields($table);
    foreach (array_keys($fields) as $field) {
        echo "  - {$field}\n";
    }
}
```

## Events

| Property | Type | Description |
|----------|------|-------------|
| `OnBeforeConnect` | `?string` | Called before connecting |
| `OnAfterConnect` | `?string` | Called after connecting |
| `OnBeforeDisconnect` | `?string` | Called before disconnecting |
| `OnAfterDisconnect` | `?string` | Called after disconnecting |

## Error Handling

Connection and query errors throw `VCL\Database\EDatabaseError`:

```php
try {
    $this->DB->Connected = true;
} catch (VCL\Database\EDatabaseError $e) {
    echo "Database error: " . $e->getMessage();
}
```

## Notes

- Uses mysqli extension (not deprecated mysql)
- Default charset is utf8mb4 (full Unicode support)
- Connection is lazy-loaded when first query is executed
- Transactions require InnoDB storage engine
