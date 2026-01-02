<?php

declare(strict_types=1);

namespace VCL\Database\MySQL;

use VCL\Database\CustomConnection;
use mysqli;

/**
 * MySQLDatabase provides a connection to MySQL databases.
 *
 * Use MySQLDatabase to connect to a MySQL database server.
 * Set Host, DatabaseName, UserName and UserPassword properties,
 * then set Connected to true to establish the connection.
 *
 * PHP 8.4 version with Property Hooks using mysqli.
 */
class MySQLDatabase extends CustomConnection
{
    public ?mysqli $_connection = null;
    protected bool $_debug = false;
    protected string $_databasename = '';
    protected string $_host = '';
    protected string $_username = '';
    protected string $_userpassword = '';
    protected string $_dictionary = '';
    protected array|false $_dictionaryproperties = false;
    protected int $_dialect = 3;
    protected int $_port = 3306;
    protected string $_charset = 'utf8mb4';

    // Property Hooks
    public bool $Debug {
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

    public string $Dictionary {
        get => $this->_dictionary;
        set => $this->_dictionary = $value;
    }

    public array|false $DictionaryProperties {
        get => $this->_dictionaryproperties;
        set => $this->_dictionaryproperties = $value;
    }

    public int $Dialect {
        get => $this->_dialect;
        set => $this->_dialect = $value;
    }

    public int $Port {
        get => $this->_port;
        set => $this->_port = max(1, $value);
    }

    public string $Charset {
        get => $this->_charset;
        set => $this->_charset = $value;
    }

    /**
     * Check if connected.
     */
    protected function readConnected(): bool
    {
        return $this->_connection !== null;
    }

    /**
     * Get field names for a table.
     */
    public function MetaFields(string $tablename): array
    {
        $result = [];
        $tablename = mysqli_real_escape_string($this->_connection, $tablename);
        $rs = $this->Execute("SHOW COLUMNS FROM `{$tablename}`");

        if ($rs !== false) {
            while ($row = mysqli_fetch_row($rs)) {
                $result[$row[0]] = '';
            }
            mysqli_free_result($rs);
        }

        return $result;
    }

    /**
     * Begin a transaction.
     */
    public function BeginTrans(): void
    {
        if ($this->_connection !== null) {
            mysqli_begin_transaction($this->_connection);
        }
    }

    /**
     * Complete a transaction.
     */
    public function CompleteTrans(bool $autocomplete = true): bool
    {
        if ($this->_connection === null) {
            return false;
        }

        if ($autocomplete) {
            return mysqli_commit($this->_connection);
        } else {
            return mysqli_rollback($this->_connection);
        }
    }

    /**
     * Format a date for MySQL.
     */
    public function DBDate(string $input): string
    {
        $timestamp = strtotime($input);
        return $timestamp !== false ? date('Y-m-d', $timestamp) : $input;
    }

    /**
     * Format a parameter.
     */
    public function Param(string $input): string
    {
        return $this->QuoteStr($input);
    }

    /**
     * Quote a string for MySQL.
     */
    public function QuoteStr(string $input): string
    {
        if ($this->_connection !== null) {
            return "'" . mysqli_real_escape_string($this->_connection, $input) . "'";
        }
        return "'" . addslashes($input) . "'";
    }

    /**
     * Execute a query.
     *
     * @return \mysqli_result|bool
     */
    public function Execute(string $query, array $params = []): \mysqli_result|bool
    {
        $this->Open();

        if ($this->_connection === null) {
            throw new \VCL\Database\EDatabaseError("No database connection");
        }

        $rs = @mysqli_query($this->_connection, $query);

        if ($rs === false) {
            $error = mysqli_error($this->_connection);
            throw new \VCL\Database\EDatabaseError("Error executing query: {$query} [{$error}]");
        }

        return $rs;
    }

    /**
     * Execute a limited query.
     *
     * @return \mysqli_result|bool
     */
    public function ExecuteLimit(string $query, int $numrows, int $offset = 0, array $params = []): \mysqli_result|bool
    {
        $sql = $query . " LIMIT {$offset}, {$numrows}";
        return $this->Execute($sql);
    }

    /**
     * Connect to the database.
     */
    protected function DoConnect(): void
    {
        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            return;
        }

        $this->_connection = @mysqli_connect(
            $this->_host,
            $this->_username,
            $this->_userpassword,
            $this->_databasename,
            $this->_port
        );

        if ($this->_connection === false) {
            $this->_connection = null;
            throw new \VCL\Database\EDatabaseError("Cannot connect to database server: " . mysqli_connect_error());
        }

        // Set charset
        if ($this->_charset !== '') {
            mysqli_set_charset($this->_connection, $this->_charset);
        }

        if ($this->_debug) {
            error_log("MySQL connected to {$this->_host}/{$this->_databasename}");
        }
    }

    /**
     * Disconnect from the database.
     */
    protected function DoDisconnect(): void
    {
        if ($this->_connection !== null) {
            mysqli_close($this->_connection);
            $this->_connection = null;

            if ($this->_debug) {
                error_log("MySQL disconnected");
            }
        }
    }

    /**
     * Get field dictionary properties.
     */
    public function readFieldDictionaryProperties(string $table, string $field): array
    {
        $result = [];
        $table = trim($table);
        $field = trim($field);

        if (!$this->readConnected()) {
            return $result;
        }

        if ($this->_dictionary !== '') {
            $tableEsc = mysqli_real_escape_string($this->_connection, $table);
            $fieldEsc = mysqli_real_escape_string($this->_connection, $field);

            $q = "SELECT * FROM {$this->_dictionary} WHERE dict_tablename='{$tableEsc}' AND dict_fieldname='{$fieldEsc}'";
            $r = $this->Execute($q);

            $props = [];
            while ($arow = mysqli_fetch_assoc($r)) {
                $row = [];
                foreach ($arow as $k => $v) {
                    $row[strtolower($k)] = $v;
                }
                $props[$row['dict_property']] = [$row['dict_value1'], $row['dict_value2']];
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
     * Get table indexes.
     */
    public function extractIndexes(string $table, bool $primary = false): array
    {
        $tableEsc = mysqli_real_escape_string($this->_connection, $table);
        $sql = "SHOW INDEX FROM `{$tableEsc}`";

        if (!$primary) {
            $sql .= " WHERE Key_name = 'PRIMARY'";
        } else {
            $sql .= " WHERE Key_name <> 'PRIMARY'";
        }

        $rs = $this->Execute($sql);
        $indexes = [];

        while ($row = mysqli_fetch_row($rs)) {
            if (!$primary && $row[2] === 'PRIMARY') {
                continue;
            }

            if (!isset($indexes[$row[2]])) {
                $indexes[$row[2]] = [
                    'unique' => ($row[1] == 0),
                    'columns' => []
                ];
            }

            $indexes[$row[2]]['columns'][$row[3] - 1] = $row[4];
        }

        // Sort columns by order in the index
        foreach (array_keys($indexes) as $index) {
            ksort($indexes[$index]['columns']);
        }

        return $indexes;
    }

    /**
     * Get all databases.
     */
    public function databases(): array
    {
        $result = [];
        $rs = $this->Execute("SHOW DATABASES");

        if ($rs !== false) {
            while ($row = mysqli_fetch_row($rs)) {
                $result[] = $row[0];
            }
            mysqli_free_result($rs);
        }

        return $result;
    }

    /**
     * Get all tables in the database.
     */
    public function tables(): array
    {
        $dbEsc = mysqli_real_escape_string($this->_connection, $this->_databasename);
        $rs = $this->Execute("SHOW TABLES FROM `{$dbEsc}`");

        $result = [];
        if ($rs !== false) {
            while ($row = mysqli_fetch_row($rs)) {
                $result[] = $row[0];
            }
            mysqli_free_result($rs);
        }

        return $result;
    }

    /**
     * Create the dictionary table.
     */
    public function createDictionaryTable(): bool
    {
        if (!$this->readConnected() || $this->_dictionary === '') {
            return false;
        }

        $q = "CREATE TABLE IF NOT EXISTS {$this->_dictionary} (
            DICT_ID INT NOT NULL AUTO_INCREMENT,
            DICT_TABLENAME VARCHAR(60),
            DICT_FIELDNAME VARCHAR(60),
            DICT_PROPERTY VARCHAR(60),
            DICT_VALUE1 VARCHAR(60),
            DICT_VALUE2 VARCHAR(200),
            PRIMARY KEY (DICT_ID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        try {
            $this->Execute($q);
            return true;
        } catch (\VCL\Database\EDatabaseError $e) {
            return false;
        }
    }

    /**
     * Get the last insert ID.
     */
    public function lastInsertId(): int
    {
        if ($this->_connection !== null) {
            return (int) mysqli_insert_id($this->_connection);
        }
        return 0;
    }

    /**
     * Get the number of affected rows.
     */
    public function affectedRows(): int
    {
        if ($this->_connection !== null) {
            return (int) mysqli_affected_rows($this->_connection);
        }
        return 0;
    }

    // Legacy getters/setters
    public function getDebug(): bool { return $this->_debug; }
    public function setDebug(bool $value): void { $this->Debug = $value; }
    public function defaultDebug(): int { return 0; }

    public function getDatabaseName(): string { return $this->_databasename; }
    public function setDatabaseName(string $value): void { $this->DatabaseName = $value; }

    public function getHost(): string { return $this->_host; }
    public function setHost(string $value): void { $this->Host = $value; }

    public function getUserName(): string { return $this->_username; }
    public function setUserName(string $value): void { $this->UserName = $value; }

    public function getUserPassword(): string { return $this->_userpassword; }
    public function setUserPassword(string $value): void { $this->UserPassword = $value; }

    public function getDictionary(): string { return $this->_dictionary; }
    public function setDictionary(string $value): void { $this->Dictionary = $value; }

    public function getDialect(): int { return $this->_dialect; }
    public function setDialect(int $value): void { $this->Dialect = $value; }
    public function defaultDialect(): int { return 3; }

    public function readDictionaryProperties(): array|false { return $this->_dictionaryproperties; }
    public function writeDictionaryProperties(array|false $value): void { $this->DictionaryProperties = $value; }
}
