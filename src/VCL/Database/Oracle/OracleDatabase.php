<?php
/**
 * VCL for PHP
 *
 * Copyright (c) 2004-2008 qadram software S.L.
 * Copyright (c) 2026 Gunnar Beushausen
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 */

declare(strict_types=1);

namespace VCL\Database\Oracle;

use VCL\Database\CustomConnection;
use VCL\Database\EDatabaseError;

/**
 * Oracle Database connection component.
 *
 * Provides connection to Oracle databases using OCI8 extension.
 *
 * Example usage:
 * ```php
 * $db = new OracleDatabase($this);
 * $db->Name = 'OracleDB1';
 * $db->Host = 'localhost';
 * $db->DatabaseName = 'ORCL';
 * $db->UserName = 'scott';
 * $db->UserPassword = 'tiger';
 * $db->Connected = true;
 * ```
 *
 * @link https://www.php.net/manual/en/book.oci8.php
 */
class OracleDatabase extends CustomConnection
{
    /** @var resource|null */
    public $connection = null;

    protected int $_debug = 0;
    protected string $_databasename = '';
    protected string $_host = '';
    protected string $_username = '';
    protected string $_userpassword = '';
    protected string $_charset = '';
    protected bool $_usesid = false;
    protected int $_dialect = 3;
    protected bool $autoCommit = true;

    public string $NLS_DATE_FORMAT = 'YYYY-MM-DD';

    // =========================================================================
    // PROPERTY HOOKS
    // =========================================================================

    public bool $Connected {
        get => $this->connection !== null;
        set {
            if ($value) {
                $this->open();
            } else {
                $this->close();
            }
        }
    }

    public int $Debug {
        get => $this->_debug;
        set => $this->_debug = $value;
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

    public string $Charset {
        get => $this->_charset;
        set => $this->_charset = $value;
    }

    public bool $UseSID {
        get => $this->_usesid;
        set => $this->_usesid = $value;
    }

    public int $Dialect {
        get => $this->_dialect;
        set => $this->_dialect = $value;
    }

    // =========================================================================
    // CONNECTION METHODS
    // =========================================================================

    /**
     * Open the database connection.
     */
    public function open(): void
    {
        if ($this->connection !== null) {
            return;
        }

        if (!extension_loaded('oci8')) {
            throw new EDatabaseError('OCI8 extension is not loaded');
        }

        $this->callEvent('onbeforeconnect', []);

        // Build connection string
        if ($this->_usesid) {
            $connectionString = sprintf(
                "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=%s)(PORT=1521))(CONNECT_DATA=(SID=%s)))",
                $this->_host,
                $this->_databasename
            );
        } else {
            $connectionString = $this->_host;
            if ($this->_databasename !== '') {
                $connectionString .= '/' . $this->_databasename;
            }
        }

        $charset = $this->_charset !== '' ? $this->_charset : null;

        $this->connection = @oci_connect(
            $this->_username,
            $this->_userpassword,
            $connectionString,
            $charset
        );

        if ($this->connection === false) {
            $error = oci_error();
            throw new EDatabaseError('Oracle connection failed: ' . ($error['message'] ?? 'Unknown error'));
        }

        // Set date format
        if ($this->NLS_DATE_FORMAT !== '') {
            $this->execute("ALTER SESSION SET NLS_DATE_FORMAT = '{$this->NLS_DATE_FORMAT}'");
        }

        $this->callEvent('onafterconnect', []);
    }

    /**
     * Close the database connection.
     */
    public function close(): void
    {
        if ($this->connection === null) {
            return;
        }

        $this->callEvent('onbeforedisconnect', []);

        @oci_close($this->connection);
        $this->connection = null;

        $this->callEvent('onafterdisconnect', []);
    }

    // =========================================================================
    // TRANSACTION METHODS
    // =========================================================================

    /**
     * Begin a transaction.
     */
    public function beginTrans(): void
    {
        $this->autoCommit = false;
    }

    /**
     * Commit the current transaction.
     */
    public function commit(): bool
    {
        if ($this->autoCommit || $this->connection === null) {
            return true;
        }

        $result = @oci_commit($this->connection);
        if (!$result) {
            $error = oci_error($this->connection);
            throw new EDatabaseError('Commit failed: ' . ($error['message'] ?? 'Unknown error'));
        }

        $this->autoCommit = true;
        return true;
    }

    /**
     * Rollback the current transaction.
     */
    public function rollback(): bool
    {
        if ($this->autoCommit || $this->connection === null) {
            return true;
        }

        $result = @oci_rollback($this->connection);
        if (!$result) {
            $error = oci_error($this->connection);
            throw new EDatabaseError('Rollback failed: ' . ($error['message'] ?? 'Unknown error'));
        }

        $this->autoCommit = true;
        return true;
    }

    /**
     * Complete a transaction.
     */
    public function completeTrans(bool $commit = true): bool
    {
        if (!$commit) {
            return $this->rollback();
        }
        return $this->commit();
    }

    // =========================================================================
    // QUERY METHODS
    // =========================================================================

    /**
     * Execute a query.
     *
     * @param string $query SQL query
     * @param array $params Bind parameters
     * @return resource|null Result set for SELECT queries
     */
    public function execute(string $query, array &$params = [])
    {
        $this->open();

        $stmt = @oci_parse($this->connection, $query);
        if ($stmt === false) {
            $error = oci_error($this->connection);
            throw new EDatabaseError('Parse error: ' . ($error['message'] ?? 'Unknown error'));
        }

        // Bind parameters
        if (!empty($params)) {
            foreach ($params as $key => &$value) {
                $len = $value === ' ' ? 1 : -1;
                oci_bind_by_name($stmt, ':' . $key, $value, $len);
            }
        }

        // Execute
        $mode = $this->autoCommit ? OCI_COMMIT_ON_SUCCESS : OCI_DEFAULT;
        $result = @oci_execute($stmt, $mode);

        if ($result === false) {
            $error = oci_error($stmt);
            throw new EDatabaseError('Execute error: ' . ($error['message'] ?? 'Unknown error'));
        }

        // Return statement for SELECT queries
        $type = strtoupper(substr(trim($query), 0, 6));
        if ($type === 'SELECT') {
            return $stmt;
        }

        return null;
    }

    /**
     * Prepare a query for execution.
     *
     * @param string $query SQL query
     * @return resource|false Prepared statement
     */
    public function prepare(string $query)
    {
        if ($this->connection === null) {
            return false;
        }

        $stmt = @oci_parse($this->connection, $query);
        return $stmt !== false ? $stmt : false;
    }

    /**
     * Get column metadata for a table.
     *
     * @param string $tableName Table name
     * @return array Column information
     */
    public function getTableColumns(string $tableName): array
    {
        $sql = "SELECT column_name, data_type, data_length, nullable
                FROM user_tab_columns
                WHERE table_name = :tablename
                ORDER BY column_id";

        $params = ['tablename' => strtoupper($tableName)];
        $result = $this->execute($sql, $params);

        $columns = [];
        while ($row = oci_fetch_assoc($result)) {
            $columns[$row['COLUMN_NAME']] = [
                'type' => $row['DATA_TYPE'],
                'length' => $row['DATA_LENGTH'],
                'nullable' => $row['NULLABLE'] === 'Y',
            ];
        }

        return $columns;
    }

    /**
     * Quote a string for use in SQL.
     */
    public function quote(string $value): string
    {
        return "'" . str_replace("'", "''", $value) . "'";
    }

    // =========================================================================
    // DEFAULT VALUE METHODS
    // =========================================================================

    protected function defaultDebug(): int
    {
        return 0;
    }

    protected function defaultCharset(): string
    {
        return '';
    }

    protected function defaultUseSID(): bool
    {
        return false;
    }

    protected function defaultDialect(): int
    {
        return 3;
    }
}
