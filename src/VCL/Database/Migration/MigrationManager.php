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
use Doctrine\DBAL\Schema\Comparator;
use VCL\Database\Connection;

/**
 * MigrationManager handles database migrations.
 *
 * This class manages the execution, tracking, and generation of database
 * migrations. It maintains a migrations table to track which migrations
 * have been executed.
 *
 * Example:
 * ```php
 * $manager = new MigrationManager($connection, [
 *     'migrations_path' => __DIR__ . '/migrations',
 *     'migrations_namespace' => 'App\\Migrations',
 * ]);
 *
 * // Run all pending migrations
 * $manager->Migrate();
 *
 * // Rollback last migration
 * $manager->Rollback();
 *
 * // Get migration status
 * $status = $manager->GetStatus();
 * ```
 */
class MigrationManager
{
    protected DBALConnection $connection;
    protected string $migrationsPath;
    protected string $migrationsNamespace;
    protected string $migrationsTable;
    protected bool $allOrNothing;

    /**
     * Create a new MigrationManager.
     *
     * @param Connection|DBALConnection $connection Database connection
     * @param array $config Configuration options:
     *   - migrations_path: Directory containing migration files
     *   - migrations_namespace: PHP namespace for migrations
     *   - migrations_table: Table name for tracking migrations (default: vcl_migrations)
     *   - all_or_nothing: Run all migrations in a single transaction (default: true)
     */
    public function __construct(Connection|DBALConnection $connection, array $config = [])
    {
        if ($connection instanceof Connection) {
            $connection->Open();
            $this->connection = $connection->Dbal();
        } else {
            $this->connection = $connection;
        }

        $this->migrationsPath = $config['migrations_path'] ?? getcwd() . '/migrations';
        $this->migrationsNamespace = $config['migrations_namespace'] ?? 'VCL\\Migrations';
        $this->migrationsTable = $config['migrations_table'] ?? 'vcl_migrations';
        $this->allOrNothing = $config['all_or_nothing'] ?? true;

        $this->EnsureMigrationsTable();
    }

    /**
     * Ensure the migrations tracking table exists.
     */
    protected function EnsureMigrationsTable(): void
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist([$this->migrationsTable])) {
            $schema = new Schema();
            $table = $schema->createTable($this->migrationsTable);
            $table->addColumn('version', 'string', ['length' => 191]);
            $table->addColumn('executed_at', 'datetime', ['notnull' => false]);
            $table->addColumn('execution_time', 'integer', ['notnull' => false]);
            $table->setPrimaryKey(['version']);

            $queries = $schema->toSql($this->connection->getDatabasePlatform());
            foreach ($queries as $query) {
                $this->connection->executeStatement($query);
            }
        }
    }

    /**
     * Get all available migration versions from the filesystem.
     */
    public function GetAvailableMigrations(): array
    {
        $migrations = [];

        if (!is_dir($this->migrationsPath)) {
            return $migrations;
        }

        $files = glob($this->migrationsPath . '/Version*.php');

        foreach ($files as $file) {
            $filename = basename($file, '.php');
            $migrations[$filename] = $file;
        }

        ksort($migrations);
        return $migrations;
    }

    /**
     * Get all executed migration versions from the database.
     */
    public function GetExecutedMigrations(): array
    {
        $result = $this->connection->executeQuery(
            "SELECT version, executed_at, execution_time FROM {$this->migrationsTable} ORDER BY version"
        );

        $executed = [];
        while ($row = $result->fetchAssociative()) {
            $executed[$row['version']] = [
                'executed_at' => $row['executed_at'],
                'execution_time' => $row['execution_time'],
            ];
        }

        return $executed;
    }

    /**
     * Get pending migrations that haven't been executed yet.
     */
    public function GetPendingMigrations(): array
    {
        $available = $this->GetAvailableMigrations();
        $executed = $this->GetExecutedMigrations();

        return array_diff_key($available, $executed);
    }

    /**
     * Get migration status information.
     */
    public function GetStatus(): array
    {
        $available = $this->GetAvailableMigrations();
        $executed = $this->GetExecutedMigrations();
        $pending = $this->GetPendingMigrations();

        $status = [];
        foreach ($available as $version => $file) {
            $status[$version] = [
                'version' => $version,
                'file' => $file,
                'executed' => isset($executed[$version]),
                'executed_at' => $executed[$version]['executed_at'] ?? null,
                'execution_time' => $executed[$version]['execution_time'] ?? null,
            ];
        }

        return [
            'total' => count($available),
            'executed' => count($executed),
            'pending' => count($pending),
            'migrations' => $status,
        ];
    }

    /**
     * Run all pending migrations.
     *
     * @param string|null $targetVersion Migrate to a specific version (null = latest)
     * @return array List of executed migrations
     */
    public function Migrate(?string $targetVersion = null): array
    {
        $pending = $this->GetPendingMigrations();
        $executed = [];

        if (empty($pending)) {
            return $executed;
        }

        if ($this->allOrNothing) {
            $this->connection->beginTransaction();
        }

        try {
            foreach ($pending as $version => $file) {
                if ($targetVersion !== null && $version > $targetVersion) {
                    break;
                }

                $this->ExecuteMigration($version, $file, 'up');
                $executed[] = $version;
            }

            if ($this->allOrNothing) {
                $this->connection->commit();
            }
        } catch (\Throwable $e) {
            if ($this->allOrNothing) {
                $this->connection->rollBack();
            }
            throw new MigrationException(
                "Migration {$version} failed: " . $e->getMessage(),
                0,
                $e
            );
        }

        return $executed;
    }

    /**
     * Rollback the last executed migration.
     *
     * @param int $steps Number of migrations to rollback (default: 1)
     * @return array List of rolled back migrations
     */
    public function Rollback(int $steps = 1): array
    {
        $executed = $this->GetExecutedMigrations();
        $available = $this->GetAvailableMigrations();
        $rolledBack = [];

        if (empty($executed)) {
            return $rolledBack;
        }

        // Get last N executed migrations
        $toRollback = array_slice(array_reverse(array_keys($executed)), 0, $steps);

        if ($this->allOrNothing) {
            $this->connection->beginTransaction();
        }

        $version = null;
        try {
            foreach ($toRollback as $version) {
                if (!isset($available[$version])) {
                    throw new MigrationException("Migration file for {$version} not found");
                }

                $this->ExecuteMigration($version, $available[$version], 'down');
                $rolledBack[] = $version;
            }

            if ($this->allOrNothing) {
                $this->connection->commit();
            }
        } catch (\Throwable $e) {
            if ($this->allOrNothing) {
                $this->connection->rollBack();
            }
            $versionInfo = $version !== null ? " of {$version}" : '';
            throw new MigrationException(
                "Rollback{$versionInfo} failed: " . $e->getMessage(),
                0,
                $e
            );
        }

        return $rolledBack;
    }

    /**
     * Reset the database by rolling back all migrations.
     */
    public function Reset(): array
    {
        $executed = $this->GetExecutedMigrations();
        return $this->Rollback(count($executed));
    }

    /**
     * Reset and re-run all migrations.
     */
    public function Refresh(): array
    {
        $this->Reset();
        return $this->Migrate();
    }

    /**
     * Execute a single migration.
     */
    protected function ExecuteMigration(string $version, string $file, string $direction): void
    {
        require_once $file;

        $className = $this->migrationsNamespace . '\\' . $version;

        if (!class_exists($className)) {
            // Try without namespace
            $className = $version;
        }

        if (!class_exists($className)) {
            throw new MigrationException("Migration class {$className} not found in {$file}");
        }

        $migration = new $className($this->connection);

        if (!$migration instanceof AbstractMigration) {
            throw new MigrationException("Migration {$className} must extend AbstractMigration");
        }

        $startTime = microtime(true);

        // Get current schema
        $schemaManager = $this->connection->createSchemaManager();
        $fromSchema = $schemaManager->introspectSchema();
        $toSchema = clone $fromSchema;

        // Execute migration
        if ($direction === 'up') {
            $migration->PreUp($toSchema);
            $migration->Up($toSchema);
            $migration->PostUp($toSchema);
        } else {
            $migration->PreDown($toSchema);
            $migration->Down($toSchema);
            $migration->PostDown($toSchema);
        }

        // Calculate and execute schema diff
        $comparator = $schemaManager->createComparator();
        $diff = $comparator->compareSchemas($fromSchema, $toSchema);
        $queries = $this->connection->getDatabasePlatform()->getAlterSchemaSQL($diff);

        foreach ($queries as $query) {
            $this->connection->executeStatement($query);
        }

        // Execute any additional SQL added via AddSql()
        foreach ($migration->GetSql() as $sql) {
            $this->connection->executeStatement($sql['sql'], $sql['params'], $sql['types']);
        }

        $executionTime = (int)((microtime(true) - $startTime) * 1000);

        // Update migrations table
        if ($direction === 'up') {
            $this->connection->insert($this->migrationsTable, [
                'version' => $version,
                'executed_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                'execution_time' => $executionTime,
            ]);
        } else {
            $this->connection->delete($this->migrationsTable, ['version' => $version]);
        }
    }

    /**
     * Execute a specific migration by version.
     */
    public function ExecuteVersion(string $version, string $direction = 'up'): void
    {
        $available = $this->GetAvailableMigrations();

        if (!isset($available[$version])) {
            throw new MigrationException("Migration {$version} not found");
        }

        $executed = $this->GetExecutedMigrations();

        if ($direction === 'up' && isset($executed[$version])) {
            throw new MigrationException("Migration {$version} has already been executed");
        }

        if ($direction === 'down' && !isset($executed[$version])) {
            throw new MigrationException("Migration {$version} has not been executed");
        }

        $this->ExecuteMigration($version, $available[$version], $direction);
    }

    /**
     * Mark a migration as executed without actually running it.
     */
    public function MarkAsExecuted(string $version): void
    {
        $this->connection->insert($this->migrationsTable, [
            'version' => $version,
            'executed_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            'execution_time' => 0,
        ]);
    }

    /**
     * Remove a migration from the executed list without rolling it back.
     */
    public function MarkAsNotExecuted(string $version): void
    {
        $this->connection->delete($this->migrationsTable, ['version' => $version]);
    }

    /**
     * Get the migrations path.
     */
    public function GetMigrationsPath(): string
    {
        return $this->migrationsPath;
    }

    /**
     * Get the migrations namespace.
     */
    public function GetMigrationsNamespace(): string
    {
        return $this->migrationsNamespace;
    }

    /**
     * Get the migrations table name.
     */
    public function GetMigrationsTable(): string
    {
        return $this->migrationsTable;
    }
}
