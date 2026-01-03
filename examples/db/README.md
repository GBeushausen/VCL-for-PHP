# VCL Database Examples

This directory contains examples demonstrating the VCL Database layer, which is built on [Doctrine DBAL](https://www.doctrine-project.org/projects/dbal.html).

## Prerequisites

Make sure you have installed dependencies:

```bash
composer install
```

## Examples

| File | Description |
|------|-------------|
| `01_connection.php` | Database connection setup using `Connection` and `ConnectionFactory` |
| `02_query.php` | Using the `Query` component for SQL queries and result navigation |
| `03_table.php` | Using the `Table` component for CRUD operations and master-detail relationships |
| `04_querybuilder.php` | Fluent query building with `QueryBuilder` |
| `05_storedproc.php` | Executing stored procedures with `StoredProc` |
| `06_transactions.php` | Transaction handling for data integrity |
| `07_schemamanager.php` | Schema introspection and manipulation with `SchemaManager` |
| `08_migrations.php` | Database migrations for versioned schema changes |

## Running Examples

Most examples use SQLite in-memory databases for simplicity:

```bash
php examples/db/01_connection.php
php examples/db/02_query.php
# ... etc
```

## Quick Reference

### Connection

```php
use VCL\Database\ConnectionFactory;

// Quick setup
$db = ConnectionFactory::MySQL('localhost', 'mydb', 'user', 'password');
$db->Open();

// Execute query
$result = $db->Execute("SELECT * FROM users WHERE id = ?", [1]);
$row = $result->fetchAssociative();

$db->Close();
```

### Query Component

```php
use VCL\Database\Query;

$query = new Query();
$query->Database = $db;
$query->SQL = ['SELECT * FROM users WHERE status = ?'];
$query->Params = ['active'];
$query->Open();

while (!$query->EOF()) {
    echo $query->username;
    $query->Next();
}
$query->Close();
```

### Table Component

```php
use VCL\Database\Table;

$table = new Table();
$table->Database = $db;
$table->TableName = 'users';
$table->Open();

// Insert
$table->Insert();
$table->username = 'alice';
$table->email = 'alice@example.com';
$table->Post();

// Update
$table->Edit();
$table->status = 'active';
$table->Post();

// Delete
$table->Delete();
```

### QueryBuilder

```php
use VCL\Database\QueryBuilder;

$qb = new QueryBuilder($db);

$users = $qb
    ->Select('id', 'username', 'email')
    ->From('users')
    ->Where('status', '=', 'active')
    ->OrderBy('username')
    ->Limit(10)
    ->FetchAll();
```

### Transactions

```php
$db->BeginTrans();

try {
    $db->ExecuteStatement("UPDATE accounts SET balance = balance - 100 WHERE id = 1");
    $db->ExecuteStatement("UPDATE accounts SET balance = balance + 100 WHERE id = 2");
    $db->Commit();
} catch (\Throwable $e) {
    $db->Rollback();
    throw $e;
}
```

## Supported Databases

- MySQL / MariaDB
- PostgreSQL
- SQLite
- SQL Server
- Oracle
- IBM DB2

See `VCL\Database\Enums\DriverType` for the complete list.
