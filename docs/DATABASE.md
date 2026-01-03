# VCL Database Layer

VCL provides a modern database abstraction layer built on [Doctrine DBAL](https://www.doctrine-project.org/projects/dbal.html). This replaces the legacy ADOdb implementation while maintaining backward compatibility.

## Table of Contents

- [Installation](#installation)
- [Quick Start](#quick-start)
- [Connection](#connection)
- [Executing Queries](#executing-queries)
- [Query Component](#query-component)
- [Table Component](#table-component)
- [StoredProc Component](#storedproc-component)
- [QueryBuilder](#querybuilder)
- [Transactions](#transactions)
- [Schema Introspection](#schema-introspection)
- [SchemaManager](#schemamanager)
- [Migrations](#migrations)
- [Legacy Compatibility](#legacy-compatibility)

## Installation

The database layer is included with VCL. Doctrine DBAL is automatically installed as a dependency when you run:

```bash
composer install
```

## Quick Start

```php
<?php
require_once 'vendor/autoload.php';

use VCL\Database\ConnectionFactory;

// Create connection
$db = ConnectionFactory::MySQL('localhost', 'mydb', 'user', 'password');
$db->Open();

// Execute query with prepared statement
$result = $db->Execute("SELECT * FROM users WHERE status = ?", ['active']);

// Fetch results
while ($row = $result->fetchAssociative()) {
    echo $row['username'] . "\n";
}

$db->Close();
```

## Connection

### Using ConnectionFactory (Recommended)

```php
use VCL\Database\ConnectionFactory;

// MySQL
$db = ConnectionFactory::MySQL('localhost', 'mydb', 'user', 'pass');

// PostgreSQL
$db = ConnectionFactory::PostgreSQL('localhost', 'mydb', 'user', 'pass');

// SQLite
$db = ConnectionFactory::SQLite('/path/to/database.db');

// SQLite in-memory
$db = ConnectionFactory::SQLiteMemory();

// SQL Server
$db = ConnectionFactory::SQLServer('localhost', 'mydb', 'user', 'pass');

// Oracle
$db = ConnectionFactory::Oracle('localhost', 'mydb', 'user', 'pass');
```

### From Configuration Array

```php
$db = ConnectionFactory::FromArray([
    'driver' => 'mysql',
    'host' => 'localhost',
    'port' => 3306,
    'database' => 'mydb',
    'username' => 'user',
    'password' => 'secret',
    'charset' => 'utf8mb4',
]);
```

### From DSN String

```php
// MySQL
$db = ConnectionFactory::FromDsn('mysql://user:pass@localhost/mydb');

// PostgreSQL with options
$db = ConnectionFactory::FromDsn('pgsql://user:pass@localhost:5432/mydb?charset=UTF8');

// SQLite
$db = ConnectionFactory::FromDsn('sqlite:///path/to/db.sqlite');
```

### From Environment Variables

```php
// Uses DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
$db = ConnectionFactory::FromEnvironment();

// Or DATABASE_URL for full DSN
// export DATABASE_URL=mysql://user:pass@localhost/mydb
$db = ConnectionFactory::FromEnvironment();
```

### Direct Connection Class

```php
use VCL\Database\Connection;
use VCL\Database\Enums\DriverType;

$db = new Connection();
$db->Driver = DriverType::MySQL;
$db->Host = 'localhost';
$db->DatabaseName = 'mydb';
$db->UserName = 'user';
$db->UserPassword = 'secret';
$db->Charset = 'utf8mb4';
$db->Open();
```

### Available Drivers

| DriverType | Description |
|------------|-------------|
| `DriverType::MySQL` | MySQL 5.7+ / 8.x |
| `DriverType::MariaDB` | MariaDB 10.x+ |
| `DriverType::PostgreSQL` | PostgreSQL 10+ |
| `DriverType::SQLite` | SQLite 3 |
| `DriverType::SQLServer` | Microsoft SQL Server |
| `DriverType::Oracle` | Oracle Database |

## Executing Queries

### SELECT Queries

```php
// Simple query
$result = $db->Execute("SELECT * FROM users");

// With parameters (prepared statement - SECURE)
$result = $db->Execute(
    "SELECT * FROM users WHERE status = ? AND role = ?",
    ['active', 'admin']
);

// Fetch all rows
$rows = $result->fetchAllAssociative();

// Fetch single row
$row = $result->fetchAssociative();

// Fetch single column
$names = $result->fetchFirstColumn();

// Iterate
while ($row = $result->fetchAssociative()) {
    // process $row
}
```

### INSERT / UPDATE / DELETE

```php
// INSERT
$affectedRows = $db->ExecuteStatement(
    "INSERT INTO users (username, email) VALUES (?, ?)",
    ['john', 'john@example.com']
);

// Get last insert ID
$id = $db->LastInsertId();

// UPDATE
$affectedRows = $db->ExecuteStatement(
    "UPDATE users SET status = ? WHERE id = ?",
    ['inactive', 123]
);

// DELETE
$affectedRows = $db->ExecuteStatement(
    "DELETE FROM users WHERE id = ?",
    [123]
);
```

### Limited Queries (Pagination)

```php
// Get 10 rows starting from offset 20
$result = $db->ExecuteLimit(
    "SELECT * FROM users ORDER BY created_at DESC",
    10,  // limit
    20   // offset
);
```

### Using QueryBuilder

```php
$qb = $db->CreateQueryBuilder();

$result = $qb
    ->select('u.id', 'u.username', 'u.email')
    ->from('users', 'u')
    ->where('u.status = :status')
    ->andWhere('u.created_at > :date')
    ->setParameter('status', 'active')
    ->setParameter('date', '2025-01-01')
    ->orderBy('u.username', 'ASC')
    ->setMaxResults(10)
    ->executeQuery();

$users = $result->fetchAllAssociative();
```

## Query Component

The `Query` class provides a dataset-based interface for executing SQL queries. It's ideal for VCL's data-aware components.

### Basic Usage

```php
use VCL\Database\Query;
use VCL\Database\ConnectionFactory;

$db = ConnectionFactory::MySQL('localhost', 'mydb', 'user', 'pass');

$query = new Query();
$query->Database = $db;
$query->SQL = ['SELECT * FROM users WHERE status = ?'];
$query->Params = ['active'];
$query->Active = true;

// Iterate through results
while (!$query->EOF()) {
    echo $query->username . "\n";
    $query->Next();
}

$query->Close();
```

### With Ordering and Filtering

```php
$query = new Query();
$query->Database = $db;
$query->SQL = ['SELECT * FROM products'];
$query->Filter = 'price > 100';
$query->OrderField = 'name';
$query->Order = 'ASC';
$query->Active = true;
```

### Pagination

```php
$query = new Query();
$query->Database = $db;
$query->SQL = ['SELECT * FROM orders'];
$query->LimitStart = 20;  // Offset
$query->LimitCount = 10;  // Limit
$query->Active = true;
```

### Fetch Methods

```php
// Open and fetch all at once
$rows = $query->FetchAll();

// Fetch first row only
$row = $query->FetchOne();

// Execute non-SELECT (INSERT/UPDATE/DELETE)
$query->SQL = ['DELETE FROM sessions WHERE expired < NOW()'];
$affectedRows = $query->ExecSQL();
```

### Events

```php
$query->OnBeforeOpen = 'handleBeforeOpen';
$query->OnAfterOpen = 'handleAfterOpen';
$query->OnBeforeClose = 'handleBeforeClose';
$query->OnAfterClose = 'handleAfterClose';
```

## Table Component

The `Table` class provides direct access to a database table with full CRUD support.

### Basic Usage

```php
use VCL\Database\Table;
use VCL\Database\ConnectionFactory;

$db = ConnectionFactory::MySQL('localhost', 'mydb', 'user', 'pass');

$table = new Table();
$table->Database = $db;
$table->TableName = 'users';
$table->Active = true;

// Iterate through all records
while (!$table->EOF()) {
    echo $table->id . ': ' . $table->username . "\n";
    $table->Next();
}
```

### Insert Records

```php
$table->Insert();
$table->username = 'newuser';
$table->email = 'new@example.com';
$table->created_at = date('Y-m-d H:i:s');
$table->Post();

// Get the new ID (for auto-increment tables)
echo "New user ID: " . $table->id;
```

### Update Records

```php
// Navigate to record
$table->First();

// Edit current record
$table->Edit();
$table->email = 'updated@example.com';
$table->Post();
```

### Delete Records

```php
// Delete current record
$table->Delete();
```

### Cancel Changes

```php
$table->Edit();
$table->username = 'temporary';
$table->Cancel();  // Reverts changes
```

### Filtering and Ordering

```php
$table = new Table();
$table->Database = $db;
$table->TableName = 'products';
$table->Filter = "category = 'electronics' AND stock > 0";
$table->OrderField = 'price';
$table->Order = 'DESC';
$table->Active = true;
```

### Master-Detail Relationships

```php
// Master table
$masterTable = new Table();
$masterTable->Database = $db;
$masterTable->TableName = 'orders';
$masterTable->Active = true;

// Detail table
$detailTable = new Table();
$detailTable->Database = $db;
$detailTable->TableName = 'order_items';
$detailTable->MasterSource = $masterDatasource;  // Datasource component
$detailTable->MasterFields = ['order_id' => 'id'];
$detailTable->Active = true;
```

### Events

```php
$table->OnBeforeInsert = 'validateInsert';
$table->OnAfterInsert = 'logInsert';
$table->OnBeforePost = 'validateChanges';
$table->OnAfterPost = 'refreshCache';
$table->OnBeforeDelete = 'confirmDelete';
$table->OnDeleteError = 'handleDeleteError';
```

### Navigation Methods

```php
$table->First();      // Move to first record
$table->Last();       // Move to last record
$table->Next();       // Move to next record
$table->Prior();      // Move to previous record
$table->MoveBy(5);    // Move forward 5 records
$table->MoveBy(-3);   // Move backward 3 records

// Check position
if ($table->EOF()) { /* At end */ }
if ($table->BOF()) { /* At beginning */ }

// Get record count
echo "Total records: " . $table->ReadRecordCount();
```

## StoredProc Component

The `StoredProc` class encapsulates stored procedure execution across different database systems. It extends `Query` and automatically generates the appropriate SQL syntax for each database driver.

### Basic Usage

```php
use VCL\Database\StoredProc;
use VCL\Database\Connection;

$db = new Connection();
$db->Driver = DriverType::MySQL;
$db->Host = 'localhost';
$db->DatabaseName = 'mydb';
$db->UserName = 'user';
$db->UserPassword = 'secret';
$db->Open();

// Create stored procedure component
$proc = new StoredProc();
$proc->Database = $db;
$proc->StoredProcName = 'GetUserById';
$proc->Params = [123];

// Execute and fetch results
$proc->Open();
while (!$proc->EOF()) {
    echo $proc->username . "\n";
    $proc->Next();
}
$proc->Close();
```

### Database-Specific SQL Generation

StoredProc automatically generates the correct syntax based on the database driver:

| Driver | Generated SQL |
|--------|---------------|
| MySQL/MariaDB | `CALL procedure_name(params)` |
| Oracle | `BEGIN procedure_name(params); END;` |
| PostgreSQL/SQLite/Others | `SELECT * FROM procedure_name(params)` |

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `StoredProcName` | `string` | Name of the stored procedure |
| `FetchQuery` | `string` | Additional query to execute after the procedure (MySQL only) |
| `Params` | `array` | Parameters to pass to the procedure |
| `Database` | `Connection` | Database connection to use |

### Using FetchQuery (MySQL)

For MySQL stored procedures that set session variables:

```php
$proc = new StoredProc();
$proc->Database = $db;
$proc->StoredProcName = 'CalculateTotal';
$proc->Params = [1001];
$proc->FetchQuery = 'SELECT @total, @tax, @discount';

$proc->Open();
$total = $proc->Fields['@total'];
```

Generated SQL: `CALL CalculateTotal('1001'); SELECT @total, @tax, @discount`

### Executing Without Result Set

For procedures that don't return results:

```php
$proc = new StoredProc();
$proc->Database = $db;
$proc->StoredProcName = 'UpdateUserStatus';
$proc->Params = [123, 'active'];

// ExecuteProc runs the procedure without expecting a result set
$proc->ExecuteProc();
```

### Examples by Database

#### MySQL

```php
// MySQL stored procedure
$proc = new StoredProc();
$proc->Database = $mysqlDb;
$proc->StoredProcName = 'sp_get_orders';
$proc->Params = ['2024-01-01', '2024-12-31'];
$proc->Open();
// Executes: CALL sp_get_orders('2024-01-01', '2024-12-31')
```

#### PostgreSQL

```php
// PostgreSQL function
$proc = new StoredProc();
$proc->Database = $pgDb;
$proc->StoredProcName = 'get_user_orders';
$proc->Params = [42];
$proc->Open();
// Executes: SELECT * FROM get_user_orders('42')
```

#### Oracle

```php
// Oracle procedure
$proc = new StoredProc();
$proc->Database = $oracleDb;
$proc->StoredProcName = 'PKG_USERS.GET_BY_ID';
$proc->Params = [42];
$proc->ExecuteProc();
// Executes: BEGIN PKG_USERS.GET_BY_ID('42'); END;
```

## QueryBuilder

The `QueryBuilder` class provides a fluent interface for building SQL queries programmatically.

### SELECT Queries

```php
use VCL\Database\QueryBuilder;

$qb = new QueryBuilder($connection);

$users = $qb
    ->Select('id', 'username', 'email')
    ->From('users', 'u')
    ->Where('status', '=', 'active')
    ->AndWhere('role', 'IN', ['admin', 'moderator'])
    ->OrderBy('username')
    ->Limit(10)
    ->FetchAll();
```

### JOIN Operations

```php
$results = $qb
    ->Select('u.username', 'o.total', 'o.created_at')
    ->From('users', 'u')
    ->LeftJoin('orders', 'o', 'o.user_id = u.id')
    ->Where('o.status', '=', 'completed')
    ->OrderBy('o.created_at', 'DESC')
    ->FetchAll();
```

### Available JOIN Methods

```php
$qb->Join('table', 'alias', 'condition');       // INNER JOIN
$qb->InnerJoin('table', 'alias', 'condition');  // INNER JOIN
$qb->LeftJoin('table', 'alias', 'condition');   // LEFT JOIN
$qb->RightJoin('table', 'alias', 'condition');  // RIGHT JOIN
```

### WHERE Conditions

```php
// Basic conditions
$qb->Where('column', '=', 'value');
$qb->AndWhere('column', '!=', 'value');
$qb->OrWhere('column', '<', 100);

// IN / NOT IN
$qb->Where('status', 'IN', ['active', 'pending']);
$qb->AndWhere('category', 'NOT IN', ['archived', 'deleted']);

// BETWEEN
$qb->Where('price', 'BETWEEN', [100, 500]);

// LIKE
$qb->Where('name', 'LIKE', '%search%');

// NULL checks
$qb->Where('deleted_at', 'IS NULL');
$qb->AndWhere('verified_at', 'IS NOT NULL');

// Raw expressions
$qb->WhereRaw('YEAR(created_at) = 2025');
$qb->AndWhereRaw('LENGTH(username) > 3');
```

### GROUP BY and HAVING

```php
$stats = $qb
    ->Select('category', 'COUNT(*) as count', 'AVG(price) as avg_price')
    ->From('products')
    ->GroupBy('category')
    ->Having('COUNT(*) > 5')
    ->FetchAll();
```

### INSERT Operations

```php
$qb->Insert('users')
    ->SetValues([
        'username' => 'john',
        'email' => 'john@example.com',
        'created_at' => date('Y-m-d H:i:s'),
    ])
    ->ExecuteStatement();
```

### UPDATE Operations

```php
$affected = $qb
    ->Update('users')
    ->Set('status', 'inactive')
    ->Set('updated_at', date('Y-m-d H:i:s'))
    ->Where('last_login', '<', '2024-01-01')
    ->ExecuteStatement();
```

### DELETE Operations

```php
$deleted = $qb
    ->Delete('sessions')
    ->Where('expired_at', '<', date('Y-m-d H:i:s'))
    ->ExecuteStatement();
```

### Fetch Methods

```php
$qb->FetchAll();     // Array of all rows
$qb->FetchOne();     // First row or false
$qb->FetchColumn();  // Array of first column values
$qb->FetchScalar();  // Single value
```

### Get SQL and Parameters

```php
$sql = $qb->GetSQL();
$params = $qb->GetParameters();
```

## Transactions

```php
// Simple transaction
$db->BeginTrans();

try {
    $db->ExecuteStatement("INSERT INTO orders ...", [...]);
    $db->ExecuteStatement("UPDATE inventory ...", [...]);
    $db->Commit();
} catch (Exception $e) {
    $db->Rollback();
    throw $e;
}

// Using CompleteTrans (legacy compatible)
$db->BeginTrans();
// ... operations ...
$success = $db->CompleteTrans(true);  // true = commit, false = rollback
```

## Schema Introspection

```php
// List all tables
$tables = $db->Tables();

// List all databases
$databases = $db->Databases();

// Get column names for a table
$fields = $db->MetaFields('users');

// Get indexes
$indexes = $db->ExtractIndexes('users', true);  // true = include primary

// Full schema introspection
$schema = $db->IntrospectSchema();

foreach ($schema->getTables() as $table) {
    echo "Table: " . $table->getName() . "\n";

    foreach ($table->getColumns() as $column) {
        echo "  - " . $column->getName() . " (" . $column->getType()->getName() . ")\n";
    }
}
```

### Using SchemaManager

```php
$sm = $db->CreateSchemaManager();

// List tables
$tables = $sm->listTableNames();

// Get table details
$columns = $sm->listTableColumns('users');
$indexes = $sm->listTableIndexes('users');
$foreignKeys = $sm->listTableForeignKeys('users');

// Check if table exists
if ($sm->tablesExist(['users'])) {
    // ...
}
```

## SchemaManager

The `SchemaManager` class provides a VCL-style wrapper around Doctrine DBAL's schema manager for database introspection and manipulation.

### Creating SchemaManager

```php
use VCL\Database\Schema\SchemaManager;

$schema = new SchemaManager($connection);
```

### Listing Database Objects

```php
// List all databases
$databases = $schema->GetDatabases();

// List all table names
$tables = $schema->GetTables();

// Get detailed table information
$tableDetails = $schema->GetTableDetails();

// Get specific table details
$usersTable = $schema->GetTable('users');
```

### Column Operations

```php
// Get all columns for a table
$columns = $schema->GetColumns('users');

// Get column names only
$names = $schema->GetColumnNames('users');

// Check if column exists
if ($schema->ColumnExists('users', 'email')) {
    // ...
}

// Add a column
$schema->AddColumn('users', 'avatar', [
    'type' => 'string',
    'length' => 255,
    'notnull' => false,
]);

// Modify a column
$schema->ModifyColumn('users', 'username', [
    'type' => 'string',
    'length' => 150,
]);

// Rename a column
$schema->RenameColumn('users', 'old_name', 'new_name');

// Drop a column
$schema->DropColumn('users', 'deprecated_field');
```

### Index Operations

```php
// Get all indexes for a table
$indexes = $schema->GetIndexes('users');

// Check if index exists
if ($schema->IndexExists('users', 'idx_email')) {
    // ...
}

// Add an index
$schema->AddIndex('users', 'idx_status', ['status']);

// Add a unique index
$schema->AddIndex('users', 'idx_email_unique', ['email'], true);

// Drop an index
$schema->DropIndex('users', 'idx_old');
```

### Foreign Key Operations

```php
// Get all foreign keys for a table
$fks = $schema->GetForeignKeys('orders');

// Add a foreign key
$schema->AddForeignKey(
    'orders',                    // Table
    'fk_orders_user',           // FK name
    ['user_id'],                // Local columns
    'users',                    // Referenced table
    ['id'],                     // Referenced columns
    ['onDelete' => 'CASCADE']   // Options
);

// Drop a foreign key
$schema->DropForeignKey('orders', 'fk_orders_user');
```

### Table Operations

```php
// Check if table exists
if ($schema->TableExists('products')) {
    // ...
}

// Create a new table
$schema->CreateTable('products', [
    'id' => ['type' => 'integer', 'autoincrement' => true],
    'name' => ['type' => 'string', 'length' => 255],
    'price' => ['type' => 'decimal', 'precision' => 10, 'scale' => 2],
    'stock' => ['type' => 'integer', 'default' => 0],
    'created_at' => ['type' => 'datetime'],
], [
    'primary' => 'id',
    'indexes' => [
        'idx_name' => 'name',
    ],
    'unique' => [
        'idx_name_unique' => 'name',
    ],
]);

// Drop a table
$schema->DropTable('old_table');

// Rename a table
$schema->RenameTable('old_name', 'new_name');

// Truncate a table (delete all rows)
$schema->TruncateTable('logs');
```

### Schema Comparison

```php
// Get the full schema
$fromSchema = $schema->IntrospectSchema();

// Make changes to a copy
$toSchema = clone $fromSchema;
$toSchema->getTable('users')->addColumn('new_col', 'string', ['length' => 100]);

// Get SQL to transform schemas
$sql = $schema->CompareSchemas($fromSchema, $toSchema);
foreach ($sql as $query) {
    echo $query . "\n";
}
```

### Access DBAL SchemaManager Directly

```php
$dbalSm = $schema->GetDbalSchemaManager();
// Use native Doctrine DBAL methods
```

## Migrations

VCL includes a built-in migration system for versioning database schema changes.

### Configuration

Create `migrations.php` in your project root:

```php
<?php
return [
    'migrations_table' => 'vcl_migrations',
    'migrations_path' => __DIR__ . '/migrations',
    'migrations_namespace' => 'App\\Migrations',
    'all_or_nothing' => true,

    'connection' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'mydb',
        'username' => 'user',
        'password' => 'secret',
    ],
];
```

### CLI Commands

```bash
# Show migration status
php bin/migrate status

# Run all pending migrations
php bin/migrate migrate

# Rollback last migration
php bin/migrate rollback

# Rollback multiple migrations
php bin/migrate rollback 3

# Reset all migrations
php bin/migrate reset

# Reset and re-run all migrations
php bin/migrate refresh

# Generate new migration
php bin/migrate generate CreateProductsTable

# Generate table creation migration
php bin/migrate create-table products

# Run specific migration
php bin/migrate migrate:up Version20250103120000_CreateUsersTable

# Rollback specific migration
php bin/migrate migrate:down Version20250103120000_CreateUsersTable
```

### Via Composer

```bash
composer migrate
composer migrate:status
composer migrate:rollback
composer migrate:reset
composer migrate:refresh
```

### Writing Migrations

```php
<?php
namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use VCL\Database\Migration\AbstractMigration;

class Version20250103120000_CreateUsersTable extends AbstractMigration
{
    public function GetDescription(): string
    {
        return 'Creates the users table';
    }

    public function Up(Schema $schema): void
    {
        $table = $schema->createTable('users');

        // Columns
        $table->addColumn('id', 'integer', [
            'autoincrement' => true,
            'unsigned' => true,
        ]);
        $table->addColumn('username', 'string', ['length' => 100]);
        $table->addColumn('email', 'string', ['length' => 255]);
        $table->addColumn('password_hash', 'string', ['length' => 255]);
        $table->addColumn('is_active', 'boolean', ['default' => true]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime', ['notnull' => false]);

        // Primary key
        $table->setPrimaryKey(['id']);

        // Indexes
        $table->addUniqueIndex(['username'], 'idx_users_username');
        $table->addUniqueIndex(['email'], 'idx_users_email');
        $table->addIndex(['is_active'], 'idx_users_active');
    }

    public function Down(Schema $schema): void
    {
        $schema->dropTable('users');
    }
}
```

### Column Types

| Type | Description | Options |
|------|-------------|---------|
| `integer` | Integer | `unsigned`, `autoincrement` |
| `bigint` | Big integer | `unsigned`, `autoincrement` |
| `smallint` | Small integer | `unsigned` |
| `string` | VARCHAR | `length` (required) |
| `text` | TEXT | - |
| `boolean` | BOOLEAN | - |
| `datetime` | DATETIME | - |
| `date` | DATE | - |
| `time` | TIME | - |
| `decimal` | DECIMAL | `precision`, `scale` |
| `float` | FLOAT | - |
| `blob` | BLOB | - |
| `json` | JSON | - |

### Common Column Options

```php
$table->addColumn('name', 'string', [
    'length' => 255,           // For string types
    'notnull' => true,         // NOT NULL (default: true)
    'default' => 'value',      // Default value
    'autoincrement' => true,   // Auto increment
    'unsigned' => true,        // Unsigned integer
    'precision' => 10,         // For decimal
    'scale' => 2,              // For decimal
    'comment' => 'Description', // Column comment
]);
```

### Adding Indexes

```php
// Simple index
$table->addIndex(['column_name'], 'idx_name');

// Composite index
$table->addIndex(['col1', 'col2'], 'idx_composite');

// Unique index
$table->addUniqueIndex(['email'], 'idx_unique_email');
```

### Foreign Keys

```php
$table->addForeignKeyConstraint(
    'other_table',           // Referenced table
    ['foreign_id'],          // Local columns
    ['id'],                  // Referenced columns
    [
        'onDelete' => 'CASCADE',
        'onUpdate' => 'CASCADE',
    ],
    'fk_constraint_name'
);
```

### Raw SQL in Migrations

```php
public function Up(Schema $schema): void
{
    // Schema changes
    $table = $schema->createTable('users');
    // ...

    // Additional raw SQL
    $this->AddSql("INSERT INTO users (username) VALUES ('admin')");
    $this->AddSql("CREATE TRIGGER ...");
}
```

### Checking Existing Schema

```php
public function Up(Schema $schema): void
{
    // Check if table exists
    if (!$this->TableExists('users')) {
        $table = $schema->createTable('users');
        // ...
    }

    // Check if column exists
    if (!$this->ColumnExists('users', 'avatar')) {
        $schema->getTable('users')->addColumn('avatar', 'string', [
            'length' => 255,
            'notnull' => false,
        ]);
    }

    // Check if index exists
    if (!$this->IndexExists('users', 'idx_email')) {
        $schema->getTable('users')->addIndex(['email'], 'idx_email');
    }
}
```

### Programmatic Migration Management

```php
use VCL\Database\Migration\MigrationManager;
use VCL\Database\Migration\MigrationGenerator;

// Create manager
$manager = new MigrationManager($db, [
    'migrations_path' => __DIR__ . '/migrations',
    'migrations_namespace' => 'App\\Migrations',
]);

// Get status
$status = $manager->GetStatus();
echo "Pending: " . $status['pending'] . "\n";

// Run migrations
$executed = $manager->Migrate();

// Rollback
$rolledBack = $manager->Rollback(1);

// Generate migration
$generator = new MigrationGenerator($db, [
    'migrations_path' => __DIR__ . '/migrations',
    'migrations_namespace' => 'App\\Migrations',
]);

$file = $generator->Generate('AddAvatarToUsers');
```

## Legacy Compatibility

The VCL database layer maintains backward compatibility with the original ADOdb-based API patterns:

```php
<?php
use VCL\Database\Connection;
use VCL\Database\Table;
use VCL\Database\Query;

// Connection (replaces legacy Database class)
$db = new Connection();
$db->DriverName = 'mysql';  // or use $db->Driver = DriverType::MySQL
$db->Host = 'localhost';
$db->DatabaseName = 'mydb';
$db->UserName = 'user';
$db->UserPassword = 'secret';
$db->Connected = true;

// Execute with prepared statements
$rs = $db->Execute("SELECT * FROM users WHERE id = ?", [123]);

// Table component
$table = new Table();
$table->Database = $db;
$table->TableName = 'users';
$table->Active = true;

while (!$table->EOF()) {
    echo $table->username . "\n";
    $table->Next();
}

// Query component
$query = new Query();
$query->Database = $db;
$query->SQL = ["SELECT * FROM users WHERE status = 'active'"];
$query->Active = true;
```

### Migration from ADOdb

| ADOdb Method | VCL/DBAL Equivalent |
|--------------|---------------------|
| `$conn->Execute($sql)` | `$db->Execute($sql, $params)` |
| `$conn->SelectLimit($sql, $n, $o)` | `$db->ExecuteLimit($sql, $n, $o)` |
| `$conn->qstr($s)` | `$db->QuoteStr($s)` (deprecated, use params) |
| `$conn->StartTrans()` | `$db->BeginTrans()` |
| `$conn->CompleteTrans()` | `$db->CompleteTrans()` |
| `$conn->MetaTables()` | `$db->Tables()` |
| `$conn->MetaColumns($t)` | `$db->MetaFields($t)` |
| `$rs->EOF` | `$result->fetchAssociative() === false` |
| `$rs->MoveNext()` | (automatic in fetch loop) |
| `$rs->fields` | `$row` (returned by fetch) |

## Error Handling

```php
use VCL\Database\EDatabaseError;

try {
    $db->Execute("INVALID SQL");
} catch (EDatabaseError $e) {
    echo "Database error: " . $e->getMessage();
}
```

## Best Practices

1. **Always use prepared statements** with parameters instead of string concatenation
2. **Use transactions** for multiple related operations
3. **Use migrations** for schema changes instead of raw SQL files
4. **Close connections** when done (`$db->Close()`)
5. **Use ConnectionFactory** for cleaner connection setup
6. **Prefer modern API** (`VCL\Database\Connection`) over legacy (`Database`)

## See Also

- [Doctrine DBAL Documentation](https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/)
- [Doctrine Migrations Documentation](https://www.doctrine-project.org/projects/doctrine-migrations/en/latest/)
