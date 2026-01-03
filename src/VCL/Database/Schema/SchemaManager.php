<?php
/**
 * This file is part of the VCL for PHP project
 *
 * Copyright (c) 2004-2008 qadram software S.L.
 * Copyright (c) 2024-2025 Gunnar Beushausen
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 */

declare(strict_types=1);

namespace VCL\Database\Schema;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use VCL\Database\Connection;
use VCL\Database\EDatabaseError;

/**
 * SchemaManager provides database schema introspection and manipulation.
 *
 * This class wraps Doctrine DBAL's SchemaManager with a VCL-style API
 * for examining and modifying database schema.
 *
 * Example:
 * ```php
 * $schema = new SchemaManager($connection);
 *
 * // List all tables
 * $tables = $schema->GetTables();
 *
 * // Get table details
 * $columns = $schema->GetColumns('users');
 * $indexes = $schema->GetIndexes('users');
 *
 * // Create a table
 * $schema->CreateTable('products', [
 *     'id' => ['type' => 'integer', 'autoincrement' => true],
 *     'name' => ['type' => 'string', 'length' => 255],
 * ], ['primary' => 'id']);
 * ```
 */
class SchemaManager
{
    protected Connection $connection;
    protected AbstractSchemaManager $sm;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->Open();
        $this->sm = $this->connection->Dbal()->createSchemaManager();
    }

    /**
     * Refresh the schema manager to pick up changes.
     */
    protected function RefreshSchemaManager(): void
    {
        $this->sm = $this->connection->Dbal()->createSchemaManager();
    }

    // -------------------------------------------------------------------------
    // Database Operations
    // -------------------------------------------------------------------------

    /**
     * Get list of all databases.
     */
    public function GetDatabases(): array
    {
        return $this->sm->listDatabases();
    }

    /**
     * Create a new database.
     */
    public function CreateDatabase(string $name): void
    {
        $this->sm->createDatabase($name);
    }

    /**
     * Drop a database.
     */
    public function DropDatabase(string $name): void
    {
        $this->sm->dropDatabase($name);
    }

    // -------------------------------------------------------------------------
    // Table Operations
    // -------------------------------------------------------------------------

    /**
     * Get list of all table names.
     */
    public function GetTables(): array
    {
        return $this->sm->listTableNames();
    }

    /**
     * Get detailed table information.
     */
    public function GetTableDetails(): array
    {
        $tables = [];
        foreach ($this->sm->listTables() as $table) {
            $tables[$table->getName()] = $this->TableToArray($table);
        }
        return $tables;
    }

    /**
     * Check if a table exists.
     */
    public function TableExists(string $tableName): bool
    {
        return $this->sm->tablesExist([$tableName]);
    }

    /**
     * Get a specific table's details.
     */
    public function GetTable(string $tableName): array
    {
        if (!$this->TableExists($tableName)) {
            throw new EDatabaseError("Table '{$tableName}' does not exist");
        }

        $table = $this->sm->introspectTable($tableName);
        return $this->TableToArray($table);
    }

    /**
     * Create a new table.
     *
     * @param string $tableName Table name
     * @param array $columns Column definitions
     * @param array $options Table options (primary, indexes, unique, foreignKeys)
     */
    public function CreateTable(string $tableName, array $columns, array $options = []): void
    {
        $schema = new Schema();
        $table = $schema->createTable($tableName);

        // Add columns
        foreach ($columns as $columnName => $columnDef) {
            $type = $columnDef['type'] ?? 'string';
            unset($columnDef['type']);
            $table->addColumn($columnName, $type, $columnDef);
        }

        // Primary key
        if (isset($options['primary'])) {
            $primary = is_array($options['primary']) ? $options['primary'] : [$options['primary']];
            $table->setPrimaryKey($primary);
        }

        // Indexes
        if (isset($options['indexes'])) {
            foreach ($options['indexes'] as $indexName => $indexColumns) {
                $cols = is_array($indexColumns) ? $indexColumns : [$indexColumns];
                $table->addIndex($cols, $indexName);
            }
        }

        // Unique indexes
        if (isset($options['unique'])) {
            foreach ($options['unique'] as $indexName => $indexColumns) {
                $cols = is_array($indexColumns) ? $indexColumns : [$indexColumns];
                $table->addUniqueIndex($cols, $indexName);
            }
        }

        // Foreign keys
        if (isset($options['foreignKeys'])) {
            foreach ($options['foreignKeys'] as $fkName => $fkDef) {
                $table->addForeignKeyConstraint(
                    $fkDef['table'],
                    $fkDef['columns'],
                    $fkDef['references'],
                    $fkDef['options'] ?? [],
                    $fkName
                );
            }
        }

        // Execute
        $queries = $schema->toSql($this->connection->Dbal()->getDatabasePlatform());
        foreach ($queries as $query) {
            $this->connection->ExecuteStatement($query);
        }

        // Refresh to pick up the new table
        $this->RefreshSchemaManager();
    }

    /**
     * Drop a table.
     */
    public function DropTable(string $tableName): void
    {
        $this->sm->dropTable($tableName);
        $this->RefreshSchemaManager();
    }

    /**
     * Rename a table.
     */
    public function RenameTable(string $oldName, string $newName): void
    {
        $this->sm->renameTable($oldName, $newName);
        $this->RefreshSchemaManager();
    }

    /**
     * Truncate a table (remove all rows).
     */
    public function TruncateTable(string $tableName): void
    {
        $platform = $this->connection->Dbal()->getDatabasePlatform();
        $sql = $platform->getTruncateTableSQL($tableName);
        $this->connection->ExecuteStatement($sql);
    }

    // -------------------------------------------------------------------------
    // Column Operations
    // -------------------------------------------------------------------------

    /**
     * Get all columns for a table.
     */
    public function GetColumns(string $tableName): array
    {
        $columns = [];
        foreach ($this->sm->listTableColumns($tableName) as $column) {
            $columns[$column->getName()] = $this->ColumnToArray($column);
        }
        return $columns;
    }

    /**
     * Get column names for a table.
     */
    public function GetColumnNames(string $tableName): array
    {
        return array_keys($this->GetColumns($tableName));
    }

    /**
     * Check if a column exists.
     */
    public function ColumnExists(string $tableName, string $columnName): bool
    {
        $columns = $this->sm->listTableColumns($tableName);
        return isset($columns[strtolower($columnName)]);
    }

    /**
     * Add a column to a table.
     */
    public function AddColumn(string $tableName, string $columnName, array $definition): void
    {
        $type = $definition['type'] ?? 'string';
        unset($definition['type']);

        $fromSchema = $this->sm->introspectSchema();
        $toSchema = clone $fromSchema;

        $table = $toSchema->getTable($tableName);
        $table->addColumn($columnName, $type, $definition);

        $this->ApplySchemaDiff($fromSchema, $toSchema);
    }

    /**
     * Modify a column.
     */
    public function ModifyColumn(string $tableName, string $columnName, array $definition): void
    {
        $type = $definition['type'] ?? null;
        unset($definition['type']);

        $fromSchema = $this->sm->introspectSchema();
        $toSchema = clone $fromSchema;

        $table = $toSchema->getTable($tableName);
        $oldColumn = $table->getColumn($columnName);

        // Get the type name from the existing column if not specified
        if ($type === null) {
            $type = \Doctrine\DBAL\Types\Type::getTypeRegistry()->lookupName($oldColumn->getType());
        }

        // Drop old column and create new one with updated definition
        $table->dropColumn($columnName);

        // Merge existing column properties with new definition
        $newDef = array_merge([
            'length' => $oldColumn->getLength(),
            'notnull' => $oldColumn->getNotnull(),
            'default' => $oldColumn->getDefault(),
            'autoincrement' => $oldColumn->getAutoincrement(),
        ], $definition);

        $table->addColumn($columnName, $type, $newDef);

        $this->ApplySchemaDiff($fromSchema, $toSchema);
    }

    /**
     * Drop a column.
     */
    public function DropColumn(string $tableName, string $columnName): void
    {
        $fromSchema = $this->sm->introspectSchema();
        $toSchema = clone $fromSchema;

        $table = $toSchema->getTable($tableName);
        $table->dropColumn($columnName);

        $this->ApplySchemaDiff($fromSchema, $toSchema);
    }

    /**
     * Rename a column.
     */
    public function RenameColumn(string $tableName, string $oldName, string $newName): void
    {
        $fromSchema = $this->sm->introspectSchema();
        $toSchema = clone $fromSchema;

        $table = $toSchema->getTable($tableName);
        $column = $table->getColumn($oldName);

        // Get type name using TypeRegistry (DBAL 4 compatible)
        $typeName = \Doctrine\DBAL\Types\Type::getTypeRegistry()->lookupName($column->getType());

        $table->addColumn($newName, $typeName, [
            'length' => $column->getLength(),
            'notnull' => $column->getNotnull(),
            'default' => $column->getDefault(),
            'autoincrement' => $column->getAutoincrement(),
        ]);
        $table->dropColumn($oldName);

        $this->ApplySchemaDiff($fromSchema, $toSchema);
    }

    // -------------------------------------------------------------------------
    // Index Operations
    // -------------------------------------------------------------------------

    /**
     * Get all indexes for a table.
     */
    public function GetIndexes(string $tableName): array
    {
        $indexes = [];
        foreach ($this->sm->listTableIndexes($tableName) as $index) {
            $indexes[$index->getName()] = $this->IndexToArray($index);
        }
        return $indexes;
    }

    /**
     * Check if an index exists.
     */
    public function IndexExists(string $tableName, string $indexName): bool
    {
        $indexes = $this->sm->listTableIndexes($tableName);
        return isset($indexes[strtolower($indexName)]);
    }

    /**
     * Add an index.
     */
    public function AddIndex(string $tableName, string $indexName, array $columns, bool $unique = false): void
    {
        $fromSchema = $this->sm->introspectSchema();
        $toSchema = clone $fromSchema;

        $table = $toSchema->getTable($tableName);
        if ($unique) {
            $table->addUniqueIndex($columns, $indexName);
        } else {
            $table->addIndex($columns, $indexName);
        }

        $this->ApplySchemaDiff($fromSchema, $toSchema);
    }

    /**
     * Drop an index.
     */
    public function DropIndex(string $tableName, string $indexName): void
    {
        $fromSchema = $this->sm->introspectSchema();
        $toSchema = clone $fromSchema;

        $table = $toSchema->getTable($tableName);
        $table->dropIndex($indexName);

        $this->ApplySchemaDiff($fromSchema, $toSchema);
    }

    // -------------------------------------------------------------------------
    // Foreign Key Operations
    // -------------------------------------------------------------------------

    /**
     * Get all foreign keys for a table.
     */
    public function GetForeignKeys(string $tableName): array
    {
        $foreignKeys = [];
        foreach ($this->sm->listTableForeignKeys($tableName) as $fk) {
            $foreignKeys[$fk->getName()] = $this->ForeignKeyToArray($fk);
        }
        return $foreignKeys;
    }

    /**
     * Add a foreign key.
     */
    public function AddForeignKey(
        string $tableName,
        string $fkName,
        array $columns,
        string $referencedTable,
        array $referencedColumns,
        array $options = []
    ): void {
        $fromSchema = $this->sm->introspectSchema();
        $toSchema = clone $fromSchema;

        $table = $toSchema->getTable($tableName);
        $table->addForeignKeyConstraint(
            $referencedTable,
            $columns,
            $referencedColumns,
            $options,
            $fkName
        );

        $this->ApplySchemaDiff($fromSchema, $toSchema);
    }

    /**
     * Drop a foreign key.
     */
    public function DropForeignKey(string $tableName, string $fkName): void
    {
        $fromSchema = $this->sm->introspectSchema();
        $toSchema = clone $fromSchema;

        $table = $toSchema->getTable($tableName);
        $table->removeForeignKey($fkName);

        $this->ApplySchemaDiff($fromSchema, $toSchema);
    }

    // -------------------------------------------------------------------------
    // Schema Operations
    // -------------------------------------------------------------------------

    /**
     * Get the full database schema.
     */
    public function IntrospectSchema(): Schema
    {
        return $this->sm->introspectSchema();
    }

    /**
     * Compare two schemas and get the SQL to transform from one to the other.
     */
    public function CompareSchemas(Schema $fromSchema, Schema $toSchema): array
    {
        $comparator = $this->sm->createComparator();
        $diff = $comparator->compareSchemas($fromSchema, $toSchema);
        return $this->connection->Dbal()->getDatabasePlatform()->getAlterSchemaSQL($diff);
    }

    /**
     * Get underlying DBAL SchemaManager.
     */
    public function GetDbalSchemaManager(): AbstractSchemaManager
    {
        return $this->sm;
    }

    // -------------------------------------------------------------------------
    // Helper Methods
    // -------------------------------------------------------------------------

    /**
     * Apply schema diff.
     */
    protected function ApplySchemaDiff(Schema $fromSchema, Schema $toSchema): void
    {
        $queries = $this->CompareSchemas($fromSchema, $toSchema);
        foreach ($queries as $query) {
            $this->connection->ExecuteStatement($query);
        }

        // Refresh to pick up schema changes
        $this->RefreshSchemaManager();
    }

    /**
     * Convert Table to array.
     */
    protected function TableToArray(Table $table): array
    {
        $columns = [];
        foreach ($table->getColumns() as $column) {
            $columns[$column->getName()] = $this->ColumnToArray($column);
        }

        $indexes = [];
        foreach ($table->getIndexes() as $index) {
            $indexes[$index->getName()] = $this->IndexToArray($index);
        }

        $foreignKeys = [];
        foreach ($table->getForeignKeys() as $fk) {
            $foreignKeys[$fk->getName()] = $this->ForeignKeyToArray($fk);
        }

        $primaryKey = null;
        $pk = $table->getPrimaryKey();
        if ($pk !== null) {
            $primaryKey = $pk->getColumns();
        }

        return [
            'name' => $table->getName(),
            'columns' => $columns,
            'indexes' => $indexes,
            'foreignKeys' => $foreignKeys,
            'primaryKey' => $primaryKey,
        ];
    }

    /**
     * Convert Column to array.
     */
    protected function ColumnToArray(Column $column): array
    {
        // Get type name - in DBAL 4, use TypeRegistry
        $type = $column->getType();
        $typeName = \Doctrine\DBAL\Types\Type::getTypeRegistry()->lookupName($type);

        return [
            'name' => $column->getName(),
            'type' => $typeName,
            'length' => $column->getLength(),
            'precision' => $column->getPrecision(),
            'scale' => $column->getScale(),
            'unsigned' => $column->getUnsigned(),
            'notnull' => $column->getNotnull(),
            'default' => $column->getDefault(),
            'autoincrement' => $column->getAutoincrement(),
            'comment' => $column->getComment(),
        ];
    }

    /**
     * Convert Index to array.
     */
    protected function IndexToArray(Index $index): array
    {
        return [
            'name' => $index->getName(),
            'columns' => $index->getColumns(),
            'unique' => $index->isUnique(),
            'primary' => $index->isPrimary(),
        ];
    }

    /**
     * Convert ForeignKey to array.
     */
    protected function ForeignKeyToArray(ForeignKeyConstraint $fk): array
    {
        return [
            'name' => $fk->getName(),
            'columns' => $fk->getLocalColumns(),
            'referencedTable' => $fk->getForeignTableName(),
            'referencedColumns' => $fk->getForeignColumns(),
            'onDelete' => $fk->onDelete(),
            'onUpdate' => $fk->onUpdate(),
        ];
    }
}
