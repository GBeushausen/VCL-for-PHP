<?php
/**
 * This file is part of the VCL for PHP project
 *
 * Copyright (c) 2004-2008 qadram software S.L. <support@qadram.com>
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
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
 * USA
 */

/**
 * Database Tables Unit - Doctrine DBAL Edition
 *
 * This file provides the Database, Table, Query, and StoredProc classes
 * for database access using Doctrine DBAL as the backend.
 *
 * For legacy compatibility, the original ADOdb-based implementation is
 * preserved in legacy/dbtables.inc.php.bak
 */

// Load dependencies via Composer autoloader if available
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Load legacy dependencies
use_unit("classes.inc.php");
use_unit("controls.inc.php");
use_unit("rtl.inc.php");

// Import modern classes
use VCL\Database\Connection;
use VCL\Database\ConnectionFactory;
use VCL\Database\Query as DbalQuery;
use VCL\Database\Table as DbalTable;
use VCL\Database\EDatabaseError;
use VCL\Database\Enums\DriverType;
use VCL\Database\Enums\DatasetState;

/**
 * Database provides discrete control over a connection to a single database.
 *
 * Use Database to specify the connection information so dataset components
 * can connect to the database. This class wraps VCL\Database\Connection
 * to provide backward compatibility with the legacy ADOdb-based API.
 *
 * New code should use VCL\Database\Connection directly.
 */
class Database extends Connection
{
    // Legacy property access via $_connection
    // Maps to the internal DBAL connection for compatibility
    public mixed $_connection {
        get => $this->Dbal();
    }

    /**
     * Legacy getter/setter compatibility for DriverName.
     */
    public function getDriverName(): string
    {
        return $this->Driver->value;
    }

    public function setDriverName(string $value): void
    {
        $this->Driver = DriverType::FromAdodbDriver($value);
    }

    public function defaultDriverName(): string
    {
        return 'mysql';
    }

    public function getDatabaseName(): string
    {
        return $this->DatabaseName;
    }

    public function setDatabaseName(string $value): void
    {
        $this->DatabaseName = $value;
    }

    public function defaultDatabaseName(): string
    {
        return '';
    }

    public function getHost(): string
    {
        return $this->Host;
    }

    public function setHost(string $value): void
    {
        $this->Host = $value;
    }

    public function defaultHost(): string
    {
        return '';
    }

    public function getUserName(): string
    {
        return $this->UserName;
    }

    public function setUserName(string $value): void
    {
        $this->UserName = $value;
    }

    public function defaultUserName(): string
    {
        return '';
    }

    public function getUserPassword(): string
    {
        return $this->UserPassword;
    }

    public function setUserPassword(string $value): void
    {
        $this->UserPassword = $value;
    }

    public function defaultUserPassword(): string
    {
        return '';
    }

    public function getDebug(): bool
    {
        return $this->Debug;
    }

    public function setDebug(bool $value): void
    {
        $this->Debug = $value;
    }

    public function defaultDebug(): int
    {
        return 0;
    }

    public function getCharset(): string
    {
        return $this->Charset;
    }

    public function setCharset(string $value): void
    {
        $this->Charset = $value;
    }

    public function defaultCharset(): string
    {
        return '';
    }

    public function getDictionary(): string
    {
        return $this->Dictionary;
    }

    public function setDictionary(string $value): void
    {
        $this->Dictionary = $value;
    }

    public function defaultDictionary(): string
    {
        return '';
    }

    public function getConnected(): bool
    {
        return $this->Connected;
    }

    public function setConnected(bool $value): void
    {
        $this->Connected = $value;
    }

    public function readConnected(): bool
    {
        return $this->ReadConnected();
    }

    public function writeConnected(bool $value): void
    {
        $this->Connected = $value;
    }

    // Legacy event accessors
    public function getOnAfterConnect(): ?string
    {
        return $this->OnAfterConnect;
    }

    public function setOnAfterConnect(?string $value): void
    {
        $this->OnAfterConnect = $value;
    }

    public function getOnBeforeConnect(): ?string
    {
        return $this->OnBeforeConnect;
    }

    public function setOnBeforeConnect(?string $value): void
    {
        $this->OnBeforeConnect = $value;
    }

    public function getOnAfterDisconnect(): ?string
    {
        return $this->OnAfterDisconnect;
    }

    public function setOnAfterDisconnect(?string $value): void
    {
        $this->OnAfterDisconnect = $value;
    }

    public function getOnBeforeDisconnect(): ?string
    {
        return $this->OnBeforeDisconnect;
    }

    public function setOnBeforeDisconnect(?string $value): void
    {
        $this->OnBeforeDisconnect = $value;
    }

    /**
     * Legacy alias for Tables().
     */
    public function tables(): array
    {
        return $this->Tables();
    }

    /**
     * Legacy alias for Databases().
     */
    public function databases(): array
    {
        return $this->Databases();
    }

    /**
     * Legacy alias for ExtractIndexes().
     */
    public function extractIndexes(string $table, bool $primary = false): array
    {
        return $this->ExtractIndexes($table, $primary);
    }

    /**
     * Legacy alias for CreateDictionaryTable().
     */
    public function createDictionaryTable(): bool
    {
        return $this->CreateDictionaryTable();
    }

    /**
     * Prepare a query (legacy compatibility).
     */
    public function Prepare(string $query): void
    {
        // Doctrine DBAL uses prepared statements by default
        // This method exists for API compatibility
    }

    /**
     * Prepare a stored procedure (legacy compatibility).
     */
    public function PrepareSP(string $query): void
    {
        // Doctrine DBAL handles this differently
        // This method exists for API compatibility
    }

    /**
     * Legacy Param() method.
     */
    public function Param(string $input): string
    {
        return $this->QuoteStr($input);
    }
}

/**
 * Table encapsulates a database table.
 *
 * Use Table to access data in a single database table. Table provides
 * direct access to every record and field in an underlying database table.
 *
 * This is an alias for VCL\Database\Table for legacy compatibility.
 */
class Table extends DbalTable
{
    // Legacy property accessors
    public function getDatabase(): mixed { return $this->Database; }
    public function setDatabase(mixed $value): void { $this->Database = $value; }

    public function getTableName(): string { return $this->TableName; }
    public function setTableName(string $value): void { $this->TableName = $value; }

    public function getActive(): bool { return $this->Active; }
    public function setActive(bool $value): void { $this->Active = $value; }

    public function getMasterSource(): mixed { return $this->MasterSource; }
    public function setMasterSource(mixed $value): void { $this->MasterSource = $value; }

    public function getMasterFields(): array { return $this->MasterFields; }
    public function setMasterFields(array $value): void { $this->MasterFields = $value; }
}

/**
 * CustomTable is the base class for Table (legacy compatibility).
 */
class CustomTable extends Table
{
}

/**
 * DBDataSet encapsulates database connectivity for descendant dataset objects (legacy compatibility).
 */
class DBDataSet extends Table
{
}

/**
 * Query represents a dataset with a result set based on an SQL statement.
 *
 * Use Query to access one or more tables in a database using SQL statements.
 *
 * This is an alias for VCL\Database\Query for legacy compatibility.
 */
class Query extends DbalQuery
{
    // Legacy property accessors
    public function getDatabase(): mixed { return $this->Database; }
    public function setDatabase(mixed $value): void { $this->Database = $value; }

    public function getSQL(): array { return $this->SQL; }
    public function setSQL(array|string $value): void { $this->SQL = is_array($value) ? $value : [$value]; }

    public function getParams(): array { return $this->Params; }
    public function setParams(array $value): void { $this->Params = $value; }

    public function getActive(): bool { return $this->Active; }
    public function setActive(bool $value): void { $this->Active = $value; }
}

/**
 * CustomQuery is the base class for Query (legacy compatibility).
 */
class CustomQuery extends Query
{
}

/**
 * StoredProc encapsulates a stored procedure in an application.
 *
 * Use a StoredProc object to execute a stored procedure on a database server.
 */
class StoredProc extends DbalQuery
{
    protected string $_storedprocname = '';
    protected string $_fetchquery = '';

    protected array $_noFetchDBs = ['oracle', 'oci8'];
    protected array $_useCall = ['mysql', 'mariadb'];

    public string $StoredProcName {
        get => $this->_storedprocname;
        set => $this->_storedprocname = $value;
    }

    public string $FetchQuery {
        get => $this->_fetchquery;
        set => $this->_fetchquery = $value;
    }

    /**
     * Prepare the stored procedure.
     */
    public function Prepare(): void
    {
        // Doctrine DBAL handles this differently
    }

    /**
     * Execute the stored procedure.
     */
    public function Execute(): void
    {
        $driverName = $this->_database->Driver->value;

        if (in_array($driverName, $this->_noFetchDBs)) {
            $this->_database->Execute($this->BuildQuery());
        } else {
            $this->Close();
            $this->Open();
        }
    }

    /**
     * Build the stored procedure query.
     */
    public function BuildQuery(): string
    {
        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            return '';
        }

        $pars = '';
        foreach ($this->_params as $val) {
            if ($pars !== '') {
                $pars .= ', ';
            }
            $pars .= $this->_database->QuoteStr($val);
        }

        if ($pars !== '') {
            $pars = "({$pars})";
        }

        $driverName = $this->_database->Driver->value;

        if (in_array($driverName, $this->_noFetchDBs)) {
            return "BEGIN {$this->_storedprocname}{$pars}; END;";
        } elseif (in_array($driverName, $this->_useCall)) {
            $result = "CALL {$this->_storedprocname}{$pars}";
            if ($this->_fetchquery !== '') {
                $result .= "; {$this->_fetchquery}";
            }
            return $result;
        } else {
            return "SELECT * FROM {$this->_storedprocname}{$pars}";
        }
    }

    // Legacy accessors
    public function getStoredProcName(): string { return $this->_storedprocname; }
    public function setStoredProcName(string $value): void { $this->StoredProcName = $value; }
    public function defaultStoredProcName(): string { return ''; }

    public function getFetchQuery(): string { return $this->_fetchquery; }
    public function setFetchQuery(string $value): void { $this->FetchQuery = $value; }
    public function defaultFetchQuery(): string { return ''; }
}
