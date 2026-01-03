# VCL Examples

This directory contains example code demonstrating various features of the VCL for PHP framework.

## Directory Structure

```
examples/
├── db/                    # Database layer examples
│   ├── 01_connection.php
│   ├── 02_query.php
│   ├── 03_table.php
│   ├── 04_querybuilder.php
│   ├── 05_storedproc.php
│   ├── 06_transactions.php
│   ├── 07_schemamanager.php
│   ├── 08_migrations.php
│   └── README.md
├── demo_simple.php        # Basic VCL page example
├── demo_advanced.php      # Advanced component usage
├── demo_htmx.php          # htmx integration example
└── README.md
```

## Running Examples

### Prerequisites

```bash
# Install dependencies
composer install

# For web examples, use the built-in PHP server or DDEV
ddev start
# or
php -S localhost:8080
```

### Database Examples

Database examples use SQLite in-memory and can be run directly:

```bash
php examples/db/01_connection.php
php examples/db/02_query.php
```

### Web Examples

Demo pages need to be accessed through a web server:

- `http://localhost:8080/examples/demo_simple.php`
- `http://localhost:8080/examples/demo_advanced.php`
- `http://localhost:8080/examples/demo_htmx.php`

## Example Categories

### Database (`db/`)

Examples for the Doctrine DBAL-based database layer:

| Example | Topics Covered |
|---------|----------------|
| Connection | ConnectionFactory, DriverType, connection lifecycle |
| Query | SQL execution, parameters, navigation, field access |
| Table | CRUD operations, master-detail relationships |
| QueryBuilder | Fluent query building, SELECT/INSERT/UPDATE/DELETE |
| StoredProc | Stored procedure execution across databases |
| Transactions | BeginTrans, Commit, Rollback, CompleteTrans |
| SchemaManager | Table/column/index introspection and manipulation |
| Migrations | Versioned schema changes, up/down migrations |

### Web Demos

| Example | Description |
|---------|-------------|
| demo_simple.php | Basic VCL page with simple controls |
| demo_advanced.php | Advanced component features and layouts |
| demo_htmx.php | AJAX functionality using htmx |

## Creating New Examples

When adding new examples:

1. Use clear, descriptive filenames
2. Include a header comment explaining the example's purpose
3. Add comprehensive inline comments
4. Handle errors gracefully
5. Clean up resources (close connections, delete temp files)
6. Update the relevant README.md
