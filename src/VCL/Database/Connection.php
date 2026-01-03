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

namespace VCL\Database;

use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Exception as DBALException;
use VCL\Database\Enums\DriverType;

/**
 * Connection provides database connectivity using Doctrine DBAL.
 *
 * Use Connection to establish a connection to a database server.
 * Set Host, DatabaseName, UserName and UserPassword properties,
 * then set Connected to true to establish the connection.
 *
 * This class replaces the legacy ADOdb-based Database class with
 * a modern Doctrine DBAL implementation while maintaining the
 * familiar VCL API.
 *
 * PHP 8.4 version with Property Hooks.
 */
class Connection extends CustomConnection
{
    protected ?DBALConnection $_dbal = null;
    protected DriverType $_driver = DriverType::MySQL;
    protected string $_databasename = '';
    protected string $_host = 'localhost';
    protected string $_username = '';
    protected string $_userpassword = '';
    protected int $_port = 0;
    protected string $_charset = '';
    protected bool $_debug = false;
    protected string $_dictionary = '';
    protected array|false $_dictionaryproperties = false;
    protected ?string $_unixsocket = null;
    protected bool $_persistent = false;

    // Property Hooks
    public DriverType $Driver {
        get => $this->_driver;
        set {
            $this->_driver = $value;
            if ($this->_port === 0) {
                $this->_port = $value->DefaultPort();
            }
            if ($this->_charset === '') {
                $this->_charset = $value->DefaultCharset();
            }
        }
    }

    public string $DriverName {
        get => $this->_driver->value;
        /** @phpstan-ignore assign.propertyType (Virtual property with type conversion) */
        set => $this->_driver = DriverType::FromAdodbDriver($value);
    }

    public string $DatabaseName {
        get => $this->_databasename;
        set => $this->_databasename = $value;
    }

    public string $Host {
        get => $this->_host;
        set => $this->_host = $value;
    }

    public string $UserName {
        get => $this->_username;
        set => $this->_username = $value;
    }

    public string $UserPassword {
        get => $this->_userpassword;
        set => $this->_userpassword = $value;
    }

    public int $Port {
        get => $this->_port > 0 ? $this->_port : $this->_driver->DefaultPort();
        set => $this->_port = max(0, $value);
    }

    public string $Charset {
        get => $this->_charset !== '' ? $this->_charset : $this->_driver->DefaultCharset();
        set => $this->_charset = $value;
    }

    public bool $Debug {
        get => $this->_debug;
        set => $this->_debug = $value;
    }

    public string $Dictionary {
        get => $this->_dictionary;
        set => $this->_dictionary = $value;
    }

    public array|false $DictionaryProperties {
        get => $this->_dictionaryproperties;
        set => $this->_dictionaryproperties = $value;
    }

    public ?string $UnixSocket {
        get => $this->_unixsocket;
        set => $this->_unixsocket = $value;
    }

    public bool $Persistent {
        get => $this->_persistent;
        set => $this->_persistent = $value;
    }

    /**
     * Returns the underlying Doctrine DBAL connection.
     */
    public function Dbal(): ?DBALConnection
    {
        return $this->_dbal;
    }

    /**
     * Check if connected.
     */
    protected function ReadConnected(): bool
    {
        return $this->_dbal !== null && $this->_dbal->isConnected();
    }

    /**
     * Connect to the database.
     */
    protected function DoConnect(): void
    {
        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            return;
        }

        $this->CallEvent('oncustomconnect', []);

        $params = $this->BuildConnectionParams();

        try {
            $this->_dbal = DriverManager::getConnection($params);

            if ($this->_debug) {
                error_log("DBAL connected to {$this->_host}/{$this->_databasename} using {$this->_driver->value}");
            }
        } catch (DBALException $e) {
            throw new EDatabaseError("Cannot connect to database server: " . $e->getMessage());
        }
    }

    /**
     * Disconnect from the database.
     */
    protected function DoDisconnect(): void
    {
        if ($this->_dbal !== null) {
            $this->_dbal->close();
            $this->_dbal = null;

            if ($this->_debug) {
                error_log("DBAL disconnected from {$this->_host}/{$this->_databasename}");
            }
        }
    }

    /**
     * Build connection parameters for Doctrine DBAL.
     */
    protected function BuildConnectionParams(): array
    {
        $params = [
            'driver' => $this->_driver->ToDbalDriver(),
            'host' => $this->_host,
            'dbname' => $this->_databasename,
            'user' => $this->_username,
            'password' => $this->_userpassword,
            'charset' => $this->Charset,
        ];

        // Add port if not default
        $port = $this->Port;
        if ($port > 0 && $this->_driver !== DriverType::SQLite) {
            $params['port'] = $port;
        }

        // SQLite specific: use path instead of dbname
        if ($this->_driver === DriverType::SQLite) {
            unset($params['host'], $params['user'], $params['password'], $params['charset']);
            if ($this->_databasename === ':memory:') {
                $params['memory'] = true;
                unset($params['dbname']);
            } else {
                $params['path'] = $this->_databasename;
                unset($params['dbname']);
            }
        }

        // Unix socket for MySQL/MariaDB
        if ($this->_unixsocket !== null && in_array($this->_driver, [DriverType::MySQL, DriverType::MariaDB])) {
            $params['unix_socket'] = $this->_unixsocket;
            unset($params['host'], $params['port']);
        }

        // Persistent connections
        if ($this->_persistent) {
            $params['persistent'] = true;
        }

        return $params;
    }

    /**
     * Execute a query and return the result.
     *
     * @param string $query SQL query with ? placeholders for parameters
     * @param array $params Parameters to bind
     * @param array $types Parameter types for binding
     * @return Result
     */
    public function Execute(string $query, array $params = [], array $types = []): Result
    {
        $this->Open();

        if ($this->_dbal === null) {
            throw new EDatabaseError("No database connection");
        }

        try {
            if ($this->_debug) {
                error_log("DBAL Execute: {$query}");
            }

            return $this->_dbal->executeQuery($query, $params, $types);
        } catch (DBALException $e) {
            throw new EDatabaseError("Error executing query: {$query} [{$e->getMessage()}]");
        }
    }

    /**
     * Execute a statement (INSERT, UPDATE, DELETE) and return affected rows.
     *
     * @param string $query SQL statement with ? placeholders
     * @param array $params Parameters to bind
     * @param array $types Parameter types for binding
     * @return int Number of affected rows
     */
    public function ExecuteStatement(string $query, array $params = [], array $types = []): int
    {
        $this->Open();

        if ($this->_dbal === null) {
            throw new EDatabaseError("No database connection");
        }

        try {
            if ($this->_debug) {
                error_log("DBAL ExecuteStatement: {$query}");
            }

            return $this->_dbal->executeStatement($query, $params, $types);
        } catch (DBALException $e) {
            throw new EDatabaseError("Error executing statement: {$query} [{$e->getMessage()}]");
        }
    }

    /**
     * Execute a limited query.
     *
     * @param string $query SQL query
     * @param int $limit Maximum rows to return
     * @param int $offset Starting offset
     * @param array $params Parameters to bind
     * @param array $types Parameter types for binding
     * @return Result
     */
    public function ExecuteLimit(string $query, int $limit, int $offset = 0, array $params = [], array $types = []): Result
    {
        $this->Open();

        if ($this->_dbal === null) {
            throw new EDatabaseError("No database connection");
        }

        try {
            $platform = $this->_dbal->getDatabasePlatform();
            $sql = $platform->modifyLimitQuery($query, $limit, $offset);

            if ($this->_debug) {
                error_log("DBAL ExecuteLimit: {$sql}");
            }

            return $this->_dbal->executeQuery($sql, $params, $types);
        } catch (DBALException $e) {
            throw new EDatabaseError("Error executing limited query: {$query} [{$e->getMessage()}]");
        }
    }

    /**
     * Begin a new transaction.
     */
    public function BeginTrans(): void
    {
        if ($this->_dbal !== null) {
            $this->_dbal->beginTransaction();
        }
    }

    /**
     * Complete the current transaction.
     */
    public function CompleteTrans(bool $autocomplete = true): bool
    {
        if ($this->_dbal === null) {
            return false;
        }

        try {
            if ($autocomplete) {
                $this->_dbal->commit();
            } else {
                $this->_dbal->rollBack();
            }
            return true;
        } catch (DBALException $e) {
            return false;
        }
    }

    /**
     * Commit the current transaction.
     */
    public function Commit(): void
    {
        if ($this->_dbal !== null) {
            $this->_dbal->commit();
        }
    }

    /**
     * Rollback the current transaction.
     */
    public function Rollback(): void
    {
        if ($this->_dbal !== null) {
            $this->_dbal->rollBack();
        }
    }

    /**
     * Get the last insert ID.
     */
    public function LastInsertId(): int|string
    {
        if ($this->_dbal === null) {
            return 0;
        }

        return $this->_dbal->lastInsertId();
    }

    /**
     * Quote a string for safe use in SQL.
     *
     * Note: Prefer using prepared statements with Execute() instead.
     */
    public function QuoteStr(string $input): string
    {
        if ($this->_dbal === null) {
            $this->Open();
        }

        return $this->_dbal->quote($input);
    }

    /**
     * Quote an identifier (table name, column name).
     */
    public function QuoteIdentifier(string $identifier): string
    {
        if ($this->_dbal === null) {
            $this->Open();
        }

        return $this->_dbal->quoteIdentifier($identifier);
    }

    /**
     * Format a date for the database.
     */
    public function DBDate(string $input): string
    {
        $timestamp = strtotime($input);
        return $timestamp !== false ? date('Y-m-d', $timestamp) : $input;
    }

    /**
     * Get field names for a table.
     */
    public function MetaFields(string $tablename): array
    {
        $this->Open();

        if ($this->_dbal === null) {
            return [];
        }

        try {
            $schemaManager = $this->_dbal->createSchemaManager();
            $columns = $schemaManager->listTableColumns($tablename);

            $result = [];
            foreach ($columns as $column) {
                $result[$column->getName()] = '';
            }

            return $result;
        } catch (DBALException $e) {
            return [];
        }
    }

    /**
     * Get all tables in the database.
     */
    public function Tables(): array
    {
        $this->Open();

        if ($this->_dbal === null) {
            return [];
        }

        try {
            $schemaManager = $this->_dbal->createSchemaManager();
            return $schemaManager->listTableNames();
        } catch (DBALException $e) {
            return [];
        }
    }

    /**
     * Get all databases (if supported).
     */
    public function Databases(): array
    {
        $this->Open();

        if ($this->_dbal === null) {
            return [];
        }

        try {
            $schemaManager = $this->_dbal->createSchemaManager();
            return $schemaManager->listDatabases();
        } catch (DBALException $e) {
            return [];
        }
    }

    /**
     * Get indexes for a table.
     */
    public function ExtractIndexes(string $table, bool $primary = false): array
    {
        $this->Open();

        if ($this->_dbal === null) {
            return [];
        }

        try {
            $schemaManager = $this->_dbal->createSchemaManager();
            $indexes = $schemaManager->listTableIndexes($table);

            $result = [];
            foreach ($indexes as $index) {
                $isPrimary = $index->isPrimary();

                // Filter based on $primary parameter
                if ($primary && !$isPrimary) {
                    continue;
                }
                if (!$primary && $isPrimary) {
                    continue;
                }

                $result[$index->getName()] = [
                    'unique' => $index->isUnique(),
                    'primary' => $isPrimary,
                    'columns' => $index->getColumns(),
                ];
            }

            return $result;
        } catch (DBALException $e) {
            return [];
        }
    }

    /**
     * Get field dictionary properties.
     */
    public function ReadFieldDictionaryProperties(string $table, string $field): array
    {
        $table = trim($table);
        $field = trim($field);
        $result = [];

        if (!$this->ReadConnected()) {
            return $result;
        }

        if ($this->_dictionary !== '') {
            $query = "SELECT * FROM {$this->_dictionary} WHERE dict_tablename = ? AND dict_fieldname = ?";
            $rs = $this->Execute($query, [$table, $field]);

            $props = [];
            while ($row = $rs->fetchAssociative()) {
                $normalizedRow = [];
                foreach ($row as $k => $v) {
                    $normalizedRow[strtolower($k)] = $v;
                }
                $props[$normalizedRow['dict_property']] = [
                    $normalizedRow['dict_value1'],
                    $normalizedRow['dict_value2']
                ];
            }

            if (!empty($props)) {
                $result = $props;
            }
        } elseif ($this->_dictionaryproperties !== false) {
            $result = $this->_dictionaryproperties[$table][$field] ?? [];
        }

        return $result;
    }

    /**
     * Create the dictionary table.
     */
    public function CreateDictionaryTable(): bool
    {
        if (!$this->ReadConnected() || $this->_dictionary === '') {
            return false;
        }

        try {
            $schemaManager = $this->_dbal->createSchemaManager();
            $schema = new \Doctrine\DBAL\Schema\Schema();

            $table = $schema->createTable($this->_dictionary);
            $table->addColumn('dict_id', 'integer', ['autoincrement' => true]);
            $table->addColumn('dict_tablename', 'string', ['length' => 60, 'notnull' => false]);
            $table->addColumn('dict_fieldname', 'string', ['length' => 60, 'notnull' => false]);
            $table->addColumn('dict_property', 'string', ['length' => 60, 'notnull' => false]);
            $table->addColumn('dict_value1', 'string', ['length' => 60, 'notnull' => false]);
            $table->addColumn('dict_value2', 'text', ['notnull' => false]);
            $table->setPrimaryKey(['dict_id']);

            $queries = $schema->toSql($this->_dbal->getDatabasePlatform());
            foreach ($queries as $query) {
                $this->_dbal->executeStatement($query);
            }

            return true;
        } catch (DBALException $e) {
            if ($this->_debug) {
                error_log("Error creating dictionary table: " . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Create a QueryBuilder instance.
     */
    public function CreateQueryBuilder(): \Doctrine\DBAL\Query\QueryBuilder
    {
        $this->Open();

        if ($this->_dbal === null) {
            throw new EDatabaseError("No database connection");
        }

        return $this->_dbal->createQueryBuilder();
    }

    /**
     * Create a SchemaManager instance.
     */
    public function CreateSchemaManager(): \Doctrine\DBAL\Schema\AbstractSchemaManager
    {
        $this->Open();

        if ($this->_dbal === null) {
            throw new EDatabaseError("No database connection");
        }

        return $this->_dbal->createSchemaManager();
    }

    /**
     * Introspect the database schema.
     */
    public function IntrospectSchema(): \Doctrine\DBAL\Schema\Schema
    {
        return $this->CreateSchemaManager()->introspectSchema();
    }
}
