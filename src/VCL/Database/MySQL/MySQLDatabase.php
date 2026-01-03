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
     *
     * @deprecated Use prepared statements with Execute() instead.
     *             Example: $db->Execute("SELECT * FROM users WHERE name = ?", [$name]);
     */
    public function QuoteStr(string $input): string
    {
        @trigger_error(
            'QuoteStr() is deprecated. Use prepared statements with Execute() instead.',
            E_USER_DEPRECATED
        );

        if ($this->_connection !== null) {
            return "'" . mysqli_real_escape_string($this->_connection, $input) . "'";
        }
        return "'" . addslashes($input) . "'";
    }

    /**
     * Escape a string for MySQL (without quotes).
     *
     * For internal use only. Prefer prepared statements.
     */
    public function escapeString(string $input): string
    {
        if ($this->_connection !== null) {
            return mysqli_real_escape_string($this->_connection, $input);
        }
        return addslashes($input);
    }

    /**
     * Execute a query.
     *
     * When $params is provided and not empty, uses prepared statements for security.
     * The query should use ? placeholders for parameters.
     *
     * @param string $query The SQL query (use ? for placeholders when using params)
     * @param array $params Optional parameters for prepared statement
     * @return \mysqli_result|bool
     *
     * @example
     * // Without params (legacy mode)
     * $db->Execute("SELECT * FROM users");
     *
     * // With prepared statement (secure mode)
     * $db->Execute("SELECT * FROM users WHERE id = ? AND status = ?", [123, 'active']);
     */
    public function Execute(string $query, array $params = []): \mysqli_result|bool
    {
        $this->Open();

        if ($this->_connection === null) {
            throw new \VCL\Database\EDatabaseError("No database connection");
        }

        // Use prepared statements if params are provided
        if (!empty($params)) {
            return $this->ExecutePrepared($query, $params);
        }

        // Legacy mode: direct query execution
        $rs = @mysqli_query($this->_connection, $query);

        if ($rs === false) {
            $error = mysqli_error($this->_connection);
            throw new \VCL\Database\EDatabaseError("Error executing query: {$query} [{$error}]");
        }

        return $rs;
    }

    /**
     * Execute a query using prepared statements.
     *
     * This method provides protection against SQL injection by using
     * mysqli prepared statements with parameter binding.
     *
     * @param string $query SQL query with ? placeholders
     * @param array $params Parameters to bind
     * @return \mysqli_result|bool
     * @throws \VCL\Database\EDatabaseError
     *
     * @example
     * $db->ExecutePrepared(
     *     "INSERT INTO users (name, email) VALUES (?, ?)",
     *     ['John', 'john@example.com']
     * );
     */
    public function ExecutePrepared(string $query, array $params): \mysqli_result|bool
    {
        $this->Open();

        if ($this->_connection === null) {
            throw new \VCL\Database\EDatabaseError("No database connection");
        }

        // Prepare the statement
        $stmt = @mysqli_prepare($this->_connection, $query);

        if ($stmt === false) {
            $error = mysqli_error($this->_connection);
            throw new \VCL\Database\EDatabaseError("Error preparing query: {$query} [{$error}]");
        }

        // Bind parameters if any
        if (!empty($params)) {
            $types = $this->getParamTypes($params);
            $bindParams = [];

            foreach ($params as $key => $value) {
                $bindParams[$key] = &$params[$key];
            }

            if (!mysqli_stmt_bind_param($stmt, $types, ...$bindParams)) {
                $error = mysqli_stmt_error($stmt);
                mysqli_stmt_close($stmt);
                throw new \VCL\Database\EDatabaseError("Error binding parameters: {$error}");
            }
        }

        // Execute the statement
        if (!mysqli_stmt_execute($stmt)) {
            $error = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            throw new \VCL\Database\EDatabaseError("Error executing prepared query: {$query} [{$error}]");
        }

        // Get result set if any
        $result = mysqli_stmt_get_result($stmt);

        // For non-SELECT queries, result will be false but that's OK
        if ($result === false) {
            // Check if it was actually an error or just no result set
            if (mysqli_stmt_errno($stmt) !== 0) {
                $error = mysqli_stmt_error($stmt);
                mysqli_stmt_close($stmt);
                throw new \VCL\Database\EDatabaseError("Error getting result: {$error}");
            }
            // For INSERT/UPDATE/DELETE, return true on success
            mysqli_stmt_close($stmt);
            return true;
        }

        mysqli_stmt_close($stmt);
        return $result;
    }

    /**
     * Get parameter types string for mysqli_stmt_bind_param.
     *
     * @param array $params The parameters
     * @return string Type string (i=integer, d=double, s=string, b=blob)
     */
    private function getParamTypes(array $params): string
    {
        $types = '';

        foreach ($params as $param) {
            if ($param === null) {
                $types .= 's';  // Treat null as string
            } elseif (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_bool($param)) {
                $types .= 'i';  // Booleans as integers
            } else {
                $types .= 's';  // Default to string
            }
        }

        return $types;
    }

    /**
     * Execute a limited query.
     *
     * @param string $query The SQL query
     * @param int $numrows Number of rows to return
     * @param int $offset Starting offset
     * @param array $params Optional parameters for prepared statement
     * @return \mysqli_result|bool
     */
    public function ExecuteLimit(string $query, int $numrows, int $offset = 0, array $params = []): \mysqli_result|bool
    {
        // Validate and sanitize limit values
        $numrows = max(0, $numrows);
        $offset = max(0, $offset);

        $sql = $query . " LIMIT {$offset}, {$numrows}";
        return $this->Execute($sql, $params);
    }

    /**
     * Connect to the database.
     */
    protected function DoConnect(): void
    {
        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            return;
        }

        $connection = @mysqli_connect(
            $this->_host,
            $this->_username,
            $this->_userpassword,
            $this->_databasename,
            $this->_port
        );

        if ($connection === false) {
            throw new \VCL\Database\EDatabaseError("Cannot connect to database server: " . mysqli_connect_error());
        }

        $this->_connection = $connection;

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
