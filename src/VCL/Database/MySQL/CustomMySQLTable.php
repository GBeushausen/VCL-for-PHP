<?php

declare(strict_types=1);

namespace VCL\Database\MySQL;

use VCL\Database\EDatabaseError;
use VCL\Database\Enums\DatasetState;

/**
 * CustomMySQLTable is the base class for MySQLTable.
 *
 * @deprecated Use VCL\Database\Table instead which provides a driver-independent
 *             implementation using Doctrine DBAL.
 *
 * Use MySQLTable to access data in a single database table using MySQL native access in PHP.
 * Table provides direct access to every record and field in an underlying database table.
 * A table component can also work with a subset of records within a database table using
 * ranges and filters.
 *
 * PHP 8.4 version with Property Hooks.
 */
class CustomMySQLTable extends MySQLDataSet
{
    protected string $_orderfield = '';
    protected string $_order = 'asc';
    protected mixed $_mastersource = null;
    protected array $_masterfields = [];

    /**
     * Validate column name to prevent SQL injection.
     *
     * Column names must start with a letter or underscore and contain only
     * alphanumeric characters and underscores.
     */
    protected function isValidColumnName(string $name): bool
    {
        return (bool) preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name);
    }

    /**
     * Get properly escaped table name with backticks.
     *
     * @throws EDatabaseError if table name is invalid
     */
    protected function getEscapedTableName(): string
    {
        $tableName = trim($this->_tablename);

        if ($tableName === '') {
            throw new EDatabaseError("Table name is empty");
        }

        // Validate table name (alphanumeric, underscore, dot for schema.table)
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*(?:\.[a-zA-Z_][a-zA-Z0-9_]*)?$/', $tableName)) {
            throw new EDatabaseError("Invalid table name: {$tableName}");
        }

        // Handle schema.table notation
        if (str_contains($tableName, '.')) {
            $parts = explode('.', $tableName, 2);
            return "`{$parts[0]}`.`{$parts[1]}`";
        }

        return "`{$tableName}`";
    }

    // Property Hooks
    public string $TableName {
        get => $this->_tablename;
        set => $this->_tablename = $value;
    }

    public string $OrderField {
        get => $this->_orderfield;
        set => $this->_orderfield = $value;
    }

    public string $Order {
        get => $this->_order;
        set => $this->_order = $value;
    }

    /**
     * Internal delete implementation.
     *
     * Uses prepared statements for security.
     */
    protected function internalDelete(): void
    {
        $whereParts = [];
        $params = [];

        foreach ($this->_keyfields as $key => $fname) {
            $val = $this->fieldbuffer[$fname] ?? '';
            if (trim((string)$val) === '') {
                continue;
            }

            // Validate column name (alphanumeric and underscore only)
            if (!$this->isValidColumnName($fname)) {
                throw new EDatabaseError("Invalid column name: {$fname}");
            }

            $whereParts[] = "`{$fname}` = ?";
            $params[] = $val;
        }

        if (!empty($whereParts)) {
            // Validate table name
            $tableName = $this->getEscapedTableName();

            $query = "DELETE FROM {$tableName} WHERE " . implode(' AND ', $whereParts);
            $this->_database->Execute($query, $params);
        }
    }

    /**
     * Delete current record.
     */
    public function Delete(): void
    {
        $this->internalDelete();
        parent::Delete();
    }

    /**
     * Get field properties from dictionary.
     */
    public function readFieldProperties(string $fieldname): mixed
    {
        if ($this->_database !== null) {
            return $this->_database->readFieldDictionaryProperties($this->_tablename, $fieldname);
        }
        return false;
    }

    /**
     * Internal post implementation.
     *
     * Uses prepared statements for security.
     */
    protected function internalPost(): void
    {
        $state = $this->_state instanceof DatasetState
            ? $this->_state
            : DatasetState::tryFrom($this->_state);

        $tableName = $this->getEscapedTableName();

        if ($state === DatasetState::Edit) {
            // Update existing record
            $whereParts = [];
            $whereParams = [];
            $buffer = $this->fieldbuffer;

            // Build WHERE clause from key fields
            foreach ($this->_keyfields as $key => $fname) {
                $val = $this->fieldbuffer[$fname] ?? '';
                unset($buffer[$fname]);
                if (trim((string)$val) === '') {
                    continue;
                }

                if (!$this->isValidColumnName($fname)) {
                    throw new EDatabaseError("Invalid column name: {$fname}");
                }

                $whereParts[] = "`{$fname}` = ?";
                $whereParams[] = $val;
            }

            // Build SET clause
            $setParts = [];
            $setParams = [];

            foreach ($buffer as $columnName => $value) {
                if (!$this->isValidColumnName($columnName)) {
                    throw new EDatabaseError("Invalid column name: {$columnName}");
                }

                $setParts[] = "`{$columnName}` = ?";
                $setParams[] = $value;
            }

            if (!empty($setParts) && !empty($whereParts)) {
                $updateSQL = "UPDATE {$tableName} SET " . implode(', ', $setParts) .
                             " WHERE " . implode(' AND ', $whereParts);

                // Combine params: SET params first, then WHERE params
                $params = array_merge($setParams, $whereParams);
                $this->_database->Execute($updateSQL, $params);
            }

            $this->_buffer = array_merge($this->_buffer, $this->fieldbuffer);
        } else {
            // Insert new record
            $columns = [];
            $placeholders = [];
            $params = [];

            foreach ($this->fieldbuffer as $columnName => $val) {
                if (!$this->isValidColumnName($columnName)) {
                    throw new EDatabaseError("Invalid column name: {$columnName}");
                }

                $columns[] = "`{$columnName}`";
                $placeholders[] = '?';
                $params[] = $val;
            }

            if (!empty($columns)) {
                $insertSQL = "INSERT INTO {$tableName} (" . implode(', ', $columns) .
                             ") VALUES (" . implode(', ', $placeholders) . ")";
                $this->_database->Execute($insertSQL, $params);
            }

            $this->_buffer = array_merge($this->_buffer, $this->fieldbuffer);
        }
    }

    /**
     * Post pending changes.
     */
    public function Post(): void
    {
        $this->internalPost();
        parent::Post();
    }

    /**
     * Build the query to send to the server.
     *
     * Uses prepared statement placeholders for master-detail values.
     * The parameters are stored in $this->_params for binding.
     */
    protected function buildQuery(): string
    {
        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            return '';
        }

        if (trim($this->_tablename) === '') {
            if ($this->isActive()) {
                throw new EDatabaseError("Missing TableName property");
            }
            return '';
        }

        // Reset params for this query
        $this->_params = [];

        // Use validated/escaped table name
        $tableName = $this->getEscapedTableName();
        $qu = "SELECT * FROM {$tableName}";

        // Validate and build ORDER BY clause
        $order = '';
        if ($this->_orderfield !== '') {
            if (!$this->isValidColumnName($this->_orderfield)) {
                throw new EDatabaseError("Invalid order field: {$this->_orderfield}");
            }

            // Validate order direction
            $orderDir = strtolower($this->_order);
            if (!in_array($orderDir, ['asc', 'desc'], true)) {
                $orderDir = 'asc';
            }

            $order = "ORDER BY `{$this->_orderfield}` {$orderDir}";
        }

        $whereParts = [];

        // Note: _filter is expected to be safe (set by developer, not user input)
        // For user-provided filters, use prepared statements separately
        if ($this->_filter !== '') {
            $whereParts[] = $this->_filter;
        }

        // Handle master-detail relationship with prepared statements
        if ($this->_mastersource !== null) {
            $this->MasterSource = $this->_mastersource;
            if (is_object($this->_mastersource)) {
                if (count($this->_masterfields) > 0) {
                    $this->_mastersource->DataSet->Open();

                    $masterConditions = [];
                    foreach ($this->_masterfields as $thisfield => $msfield) {
                        // Validate column names
                        if (!$this->isValidColumnName($thisfield)) {
                            throw new EDatabaseError("Invalid master field name: {$thisfield}");
                        }

                        $msValue = $this->_mastersource->DataSet->$msfield ?? '';
                        $masterConditions[] = "`{$thisfield}` = ?";
                        $this->_params[] = (string) $msValue;
                    }

                    // Always has items since count($this->_masterfields) > 0
                    $whereParts[] = '(' . implode(' AND ', $masterConditions) . ')';
                }
            }
        }

        $where = '';
        if (!empty($whereParts)) {
            $where = " WHERE " . implode(' AND ', $whereParts);
        }

        $result = "{$qu}{$where} {$order}";
        $this->_lastquery = $result;

        return $result;
    }

    /**
     * Return an array containing the row values.
     */
    public function readAssociativeFieldValues(): array
    {
        if ($this->isActive()) {
            return $this->_buffer;
        }
        return [];
    }

    /**
     * Return an array with Key fields for the table.
     */
    public function readKeyFields(): array
    {
        if ($this->_tablename === '' || $this->_database === null) {
            return [];
        }

        $indexes = $this->_database->extractIndexes($this->_tablename, true);

        if (is_array($indexes) && !empty($indexes)) {
            $primary = reset($indexes);

            if (isset($primary['columns']) && is_array($primary['columns'])) {
                $result = $primary['columns'];
                foreach ($result as $k => $v) {
                    $result[$k] = trim($v);
                }
                return $result;
            }
        }

        return [];
    }

    /**
     * Dump hidden key fields for form submission.
     *
     * Uses proper HTML escaping to prevent XSS.
     */
    public function dumpHiddenKeyFields(string $basename, array $values = []): string
    {
        $keyfields = $this->readKeyFields();

        if (empty($values)) {
            $values = $this->readAssociativeFieldValues();
        }

        $output = '';
        foreach ($keyfields as $k => $v) {
            $avalue = $values[$v] ?? '';
            // Properly escape all HTML attributes to prevent XSS
            $escapedBasename = htmlspecialchars($basename, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $escapedV = htmlspecialchars($v, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $escapedValue = htmlspecialchars((string) $avalue, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $output .= "<input type=\"hidden\" name=\"{$escapedBasename}[{$escapedV}]\" value=\"{$escapedValue}\" />";
        }

        return $output;
    }

    // Legacy getters/setters
    public function readTableName(): string { return $this->_tablename; }
    public function writeTableName(string $value): void { $this->TableName = $value; }
    public function defaultTableName(): string { return ''; }

    public function readOrderField(): string { return $this->_orderfield; }
    public function writeOrderField(string $value): void { $this->OrderField = $value; }
    public function defaultOrderField(): string { return ''; }

    public function readOrder(): string { return $this->_order; }
    public function writeOrder(string $value): void { $this->Order = $value; }
    public function defaultOrder(): string { return 'asc'; }
}
