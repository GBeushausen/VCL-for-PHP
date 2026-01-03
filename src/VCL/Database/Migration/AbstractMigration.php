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

namespace VCL\Database\Migration;

use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use VCL\Database\Connection;

/**
 * AbstractMigration is the base class for all database migrations.
 *
 * Extend this class to create migrations that modify your database schema.
 * Each migration should implement the Up() method for applying changes
 * and optionally the Down() method for reverting them.
 *
 * Example:
 * ```php
 * class Version20250103120000_CreateUsersTable extends AbstractMigration
 * {
 *     public function Up(Schema $schema): void
 *     {
 *         $table = $schema->createTable('users');
 *         $table->addColumn('id', 'integer', ['autoincrement' => true]);
 *         $table->addColumn('username', 'string', ['length' => 255]);
 *         $table->addColumn('email', 'string', ['length' => 255]);
 *         $table->addColumn('created_at', 'datetime');
 *         $table->setPrimaryKey(['id']);
 *         $table->addUniqueIndex(['email']);
 *     }
 *
 *     public function Down(Schema $schema): void
 *     {
 *         $schema->dropTable('users');
 *     }
 * }
 * ```
 */
abstract class AbstractMigration
{
    protected DBALConnection $connection;
    protected array $sql = [];
    protected bool $transactional = true;

    public function __construct(DBALConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Apply the migration.
     *
     * Override this method to define schema changes when migrating up.
     */
    abstract public function Up(Schema $schema): void;

    /**
     * Revert the migration.
     *
     * Override this method to define how to revert schema changes.
     * If not implemented, the migration cannot be rolled back.
     */
    public function Down(Schema $schema): void
    {
        throw new MigrationException("Migration " . static::class . " cannot be reverted.");
    }

    /**
     * Called before Up() is executed.
     */
    public function PreUp(Schema $schema): void
    {
        // Override in subclass if needed
    }

    /**
     * Called after Up() is executed.
     */
    public function PostUp(Schema $schema): void
    {
        // Override in subclass if needed
    }

    /**
     * Called before Down() is executed.
     */
    public function PreDown(Schema $schema): void
    {
        // Override in subclass if needed
    }

    /**
     * Called after Down() is executed.
     */
    public function PostDown(Schema $schema): void
    {
        // Override in subclass if needed
    }

    /**
     * Add a SQL statement to be executed.
     */
    public function AddSql(string $sql, array $params = [], array $types = []): void
    {
        $this->sql[] = [
            'sql' => $sql,
            'params' => $params,
            'types' => $types,
        ];
    }

    /**
     * Get all SQL statements added via AddSql().
     */
    public function GetSql(): array
    {
        return $this->sql;
    }

    /**
     * Clear all SQL statements.
     */
    public function ClearSql(): void
    {
        $this->sql = [];
    }

    /**
     * Execute a raw SQL query directly.
     */
    protected function ExecuteQuery(string $sql, array $params = []): void
    {
        $this->connection->executeStatement($sql, $params);
    }

    /**
     * Check if a table exists.
     */
    protected function TableExists(string $tableName): bool
    {
        $schemaManager = $this->connection->createSchemaManager();
        return $schemaManager->tablesExist([$tableName]);
    }

    /**
     * Check if a column exists in a table.
     */
    protected function ColumnExists(string $tableName, string $columnName): bool
    {
        if (!$this->TableExists($tableName)) {
            return false;
        }

        $schemaManager = $this->connection->createSchemaManager();
        $columns = $schemaManager->listTableColumns($tableName);

        return isset($columns[strtolower($columnName)]);
    }

    /**
     * Check if an index exists on a table.
     */
    protected function IndexExists(string $tableName, string $indexName): bool
    {
        if (!$this->TableExists($tableName)) {
            return false;
        }

        $schemaManager = $this->connection->createSchemaManager();
        $indexes = $schemaManager->listTableIndexes($tableName);

        return isset($indexes[strtolower($indexName)]);
    }

    /**
     * Whether this migration should run in a transaction.
     */
    public function IsTransactional(): bool
    {
        return $this->transactional;
    }

    /**
     * Set whether this migration should run in a transaction.
     */
    public function SetTransactional(bool $transactional): void
    {
        $this->transactional = $transactional;
    }

    /**
     * Get the underlying DBAL connection.
     */
    protected function GetConnection(): DBALConnection
    {
        return $this->connection;
    }

    /**
     * Get a description of this migration.
     *
     * Override this method to provide a human-readable description.
     */
    public function GetDescription(): string
    {
        return '';
    }

    /**
     * Warn about something during migration.
     */
    protected function Warn(string $message): void
    {
        // In a full implementation, this would use a logger
        error_log("[Migration Warning] " . $message);
    }

    /**
     * Write informational message during migration.
     */
    protected function Write(string $message): void
    {
        // In a full implementation, this would use a logger
        error_log("[Migration] " . $message);
    }
}
