<?php

declare(strict_types=1);

namespace VCL\Database\MySQL;

use VCL\Database\EDatabaseError;
use VCL\Database\Enums\DatasetState;

/**
 * CustomMySQLTable is the base class for MySQLTable.
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
     */
    protected function internalDelete(): void
    {
        $where = '';
        foreach ($this->_keyfields as $key => $fname) {
            $val = $this->fieldbuffer[$fname] ?? '';
            if (trim((string)$val) === '') {
                continue;
            }
            if ($where !== '') {
                $where .= ' and ';
            }
            $where .= " {$fname} = " . $this->_database->QuoteStr((string)$val);
        }

        if ($where !== '') {
            $query = "DELETE FROM {$this->_tablename} WHERE {$where}";
            $this->_database->Execute($query);
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
     */
    protected function internalPost(): void
    {
        $state = $this->_state instanceof DatasetState
            ? $this->_state
            : DatasetState::tryFrom($this->_state);

        if ($state === DatasetState::Edit) {
            // Update existing record
            $where = '';
            $buffer = $this->fieldbuffer;

            foreach ($this->_keyfields as $key => $fname) {
                $val = $this->fieldbuffer[$fname] ?? '';
                unset($buffer[$fname]);
                if (trim((string)$val) === '') {
                    continue;
                }
                if ($where !== '') {
                    $where .= ' and ';
                }
                $where .= " {$fname} = " . $this->_database->QuoteStr((string)$val);
            }

            $set = '';
            foreach ($buffer as $key => $fname) {
                if ($set !== '') {
                    $set .= ', ';
                }
                $set .= " {$key} = '{$fname}' ";
            }

            $updateSQL = "UPDATE {$this->_tablename} SET {$set} WHERE {$where}";
            $this->_database->Execute($updateSQL);
            $this->_buffer = array_merge($this->_buffer, $this->fieldbuffer);
        } else {
            // Insert new record
            $fields = '';
            $values = '';

            foreach ($this->fieldbuffer as $key => $val) {
                if ($fields !== '') {
                    $fields .= ',';
                }
                $fields .= $key;

                if ($values !== '') {
                    $values .= ',';
                }
                $values .= $this->_database->QuoteStr((string)$val);
            }

            $insertSQL = "INSERT INTO {$this->_tablename}({$fields}) VALUES ({$values})";
            $this->_database->Execute($insertSQL);
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

        $qu = "SELECT * FROM {$this->_tablename}";

        $order = '';
        if ($this->_orderfield !== '') {
            $order = "ORDER BY {$this->_orderfield} {$this->_order}";
        }

        $where = '';
        if ($this->_filter !== '') {
            $where .= " {$this->_filter} ";
        }

        // Handle master-detail relationship
        if ($this->_mastersource !== null) {
            $this->MasterSource = $this->_mastersource;
            if (is_object($this->_mastersource)) {
                if (count($this->_masterfields) > 0) {
                    $this->_mastersource->DataSet->Open();

                    $ms = '';
                    foreach ($this->_masterfields as $thisfield => $msfield) {
                        if ($ms !== '') {
                            $ms .= ' and ';
                        }
                        $msValue = $this->_mastersource->DataSet->$msfield ?? '';
                        $ms .= " {$thisfield}=" . $this->_database->QuoteStr((string)$msValue) . " ";
                    }

                    if ($ms !== '') {
                        if ($where !== '') {
                            $where .= ' and ';
                        }
                        $where .= " ({$ms}) ";
                    }
                }
            }
        }

        if ($where !== '') {
            $where = " WHERE {$where} ";
        }

        $result = "{$qu} {$where} {$order}";
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
            $avalue = str_replace('"', '&quot;', (string)$avalue);
            $output .= "<input type=\"hidden\" name=\"{$basename}[{$v}]\" value=\"{$avalue}\" />";
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
