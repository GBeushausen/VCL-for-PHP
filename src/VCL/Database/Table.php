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

use VCL\Database\Enums\DatasetState;

/**
 * Table encapsulates a database table.
 *
 * Use Table to access data in a single database table. Table provides
 * direct access to every record and field in an underlying database table.
 * It uses Doctrine DBAL for database operations.
 *
 * Example:
 * ```php
 * $table = new Table();
 * $table->Database = $connection;
 * $table->TableName = 'users';
 * $table->Active = true;
 *
 * while (!$table->EOF) {
 *     echo $table->username . "\n";
 *     $table->Next();
 * }
 *
 * // Insert new record
 * $table->Insert();
 * $table->username = 'newuser';
 * $table->email = 'new@example.com';
 * $table->Post();
 *
 * // Update record
 * $table->Edit();
 * $table->email = 'updated@example.com';
 * $table->Post();
 *
 * // Delete record
 * $table->Delete();
 * ```
 */
class Table extends DataSet
{
    protected ?Connection $_database = null;
    protected string $_tablename = '';
    protected string $_filter = '';
    protected string $_orderfield = '';
    protected string $_order = 'ASC';
    protected string $_hasautoinc = '1';
    protected array $_keyfields = [];
    protected ?array $_resultSet = null;
    protected int $_currentIndex = 0;
    protected array $_originalValues = [];

    // Events
    protected ?string $_onbeforeopen = null;
    protected ?string $_onafteropen = null;
    protected ?string $_onbeforeclose = null;
    protected ?string $_onafterclose = null;
    protected ?string $_onbeforeinsert = null;
    protected ?string $_onafterinsert = null;
    protected ?string $_onbeforeedit = null;
    protected ?string $_onafteredit = null;
    protected ?string $_onbeforepost = null;
    protected ?string $_onafterpost = null;
    protected ?string $_onbeforecancel = null;
    protected ?string $_onaftercancel = null;
    protected ?string $_onbeforedelete = null;
    protected ?string $_onafterdelete = null;
    protected ?string $_ondeleteerror = null;

    // Property Hooks
    public ?Connection $Database {
        get => $this->_database;
        set => $this->_database = $this->FixupProperty($value);
    }

    public string $TableName {
        get => $this->_tablename;
        set => $this->_tablename = $value;
    }

    public string $Filter {
        get => $this->_filter;
        set => $this->_filter = $value;
    }

    public string $OrderField {
        get => $this->_orderfield;
        set => $this->_orderfield = $value;
    }

    public string $Order {
        get => $this->_order;
        set => $this->_order = strtoupper($value) === 'DESC' ? 'DESC' : 'ASC';
    }

    public string $HasAutoInc {
        get => $this->_hasautoinc;
        set => $this->_hasautoinc = $value;
    }

    /**
     * Current record fields as associative array.
     * Use this for type-safe field access: $table->Fields['fieldname']
     */
    public array $Fields {
        get => $this->fieldbuffer;
        set => $this->fieldbuffer = $value;
    }

    // Event properties
    public ?string $OnBeforeOpen {
        get => $this->_onbeforeopen;
        set => $this->_onbeforeopen = $value;
    }

    public ?string $OnAfterOpen {
        get => $this->_onafteropen;
        set => $this->_onafteropen = $value;
    }

    public ?string $OnBeforeClose {
        get => $this->_onbeforeclose;
        set => $this->_onbeforeclose = $value;
    }

    public ?string $OnAfterClose {
        get => $this->_onafterclose;
        set => $this->_onafterclose = $value;
    }

    public ?string $OnBeforeInsert {
        get => $this->_onbeforeinsert;
        set => $this->_onbeforeinsert = $value;
    }

    public ?string $OnAfterInsert {
        get => $this->_onafterinsert;
        set => $this->_onafterinsert = $value;
    }

    public ?string $OnBeforeEdit {
        get => $this->_onbeforeedit;
        set => $this->_onbeforeedit = $value;
    }

    public ?string $OnAfterEdit {
        get => $this->_onafteredit;
        set => $this->_onafteredit = $value;
    }

    public ?string $OnBeforePost {
        get => $this->_onbeforepost;
        set => $this->_onbeforepost = $value;
    }

    public ?string $OnAfterPost {
        get => $this->_onafterpost;
        set => $this->_onafterpost = $value;
    }

    public ?string $OnBeforeCancel {
        get => $this->_onbeforecancel;
        set => $this->_onbeforecancel = $value;
    }

    public ?string $OnAfterCancel {
        get => $this->_onaftercancel;
        set => $this->_onaftercancel = $value;
    }

    public ?string $OnBeforeDelete {
        get => $this->_onbeforedelete;
        set => $this->_onbeforedelete = $value;
    }

    public ?string $OnAfterDelete {
        get => $this->_onafterdelete;
        set => $this->_onafterdelete = $value;
    }

    public ?string $OnDeleteError {
        get => $this->_ondeleteerror;
        set => $this->_ondeleteerror = $value;
    }

    public function Loaded(): void
    {
        $this->Database = $this->_database;
        parent::Loaded();
    }

    /**
     * Build the SQL query string.
     */
    public function BuildQuery(): string
    {
        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            return '';
        }

        if (trim($this->_tablename) === '') {
            if ($this->Active) {
                throw new EDatabaseError("Missing TableName property");
            }
            return '';
        }

        $sql = "SELECT * FROM " . $this->_database->QuoteIdentifier($this->_tablename);

        $where = '';
        if ($this->_filter !== '') {
            $where = $this->_filter;
        }

        // Master/Detail support
        if (is_object($this->_mastersource)) {
            $masterDataset = $this->_mastersource->DataSet ?? null;
            if ($masterDataset !== null && !empty($this->MasterFields)) {
                // Only open if not already active (don't reset cursor!)
                if (!$masterDataset->Active) {
                    $masterDataset->Open();
                }

                $ms = '';
                foreach ($this->MasterFields as $thisfield => $msfield) {
                    if ($ms !== '') {
                        $ms .= ' AND ';
                    }
                    $msValue = $masterDataset->$msfield;
                    $ms .= "{$thisfield} = " . $this->_database->QuoteStr($msValue);
                }

                if ($ms !== '') {
                    $where = $where !== '' ? "{$where} AND ({$ms})" : $ms;
                }
            }
        }

        if ($where !== '') {
            $sql .= " WHERE {$where}";
        }

        if ($this->_orderfield !== '') {
            $sql .= " ORDER BY {$this->_orderfield} {$this->_order}";
        }

        return $sql;
    }

    /**
     * Check that a database is assigned.
     */
    protected function CheckDatabase(): void
    {
        if ($this->_database === null) {
            throw new EDatabaseError("No Database assigned");
        }
    }

    /**
     * Read the primary key fields.
     */
    public function ReadKeyFields(): array
    {
        if ($this->_tablename === '' || $this->_database === null) {
            return [];
        }

        $indexes = $this->_database->ExtractIndexes($this->_tablename, true);

        foreach ($indexes as $name => $index) {
            if ($index['primary'] ?? false) {
                return array_map('trim', $index['columns']);
            }
        }

        // Fallback: try to get PRIMARY key
        $allIndexes = $this->_database->ExtractIndexes($this->_tablename, false);
        if (!empty($allIndexes)) {
            $first = reset($allIndexes);
            return array_map('trim', $first['columns'] ?? []);
        }

        return [];
    }

    /**
     * Open the table and fetch results.
     */
    public function InternalOpen(): void
    {
        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            return;
        }

        $sql = $this->BuildQuery();
        if (trim($sql) === '') {
            return;
        }

        $this->CheckDatabase();
        $this->_database->Open();

        $limitstart = $this->LimitStart;
        $limitcount = $this->LimitCount;

        if ($limitstart === 0 && $limitcount === 0) {
            $result = $this->_database->Execute($sql);
        } else {
            $result = $this->_database->ExecuteLimit($sql, $limitcount, $limitstart);
        }

        $this->_resultSet = $result->fetchAllAssociative();
        $this->_currentIndex = 0;
        $this->_recordcount = count($this->_resultSet);
        $this->_keyfields = $this->ReadKeyFields();

        if (!empty($this->_resultSet)) {
            $this->fieldbuffer = $this->_resultSet[0];
        } elseif ($this->_tablename !== '') {
            $this->fieldbuffer = $this->_database->MetaFields($this->_tablename);
        } else {
            $this->fieldbuffer = [];
        }
    }

    /**
     * Close the table.
     */
    public function InternalClose(): void
    {
        $this->_resultSet = null;
        $this->_currentIndex = 0;
        $this->_recordcount = 0;
        $this->fieldbuffer = [];
        $this->_originalValues = [];
    }

    /**
     * Open the dataset.
     */
    public function Open(): void
    {
        $this->CallEvent('onbeforeopen', []);
        $this->InternalOpen();
        $this->_state = DatasetState::Browse;
        $this->CallEvent('onafteropen', []);
    }

    /**
     * Close the dataset.
     */
    public function Close(): void
    {
        $this->CallEvent('onbeforeclose', []);
        $this->InternalClose();
        $this->_state = DatasetState::Inactive;
        $this->CallEvent('onafterclose', []);
    }

    /**
     * Move to first record.
     */
    public function First(): void
    {
        $this->InternalFirst();
    }

    /**
     * Move to first record (internal).
     */
    public function InternalFirst(): void
    {
        if ($this->_resultSet !== null && !empty($this->_resultSet)) {
            $this->_currentIndex = 0;
            $this->fieldbuffer = $this->_resultSet[0];
            $this->_recno = 0;
        }
    }

    /**
     * Move to last record.
     */
    public function Last(): void
    {
        $this->InternalLast();
    }

    /**
     * Move to last record (internal).
     */
    public function InternalLast(): void
    {
        if ($this->_resultSet !== null && !empty($this->_resultSet)) {
            $this->_currentIndex = count($this->_resultSet) - 1;
            $this->fieldbuffer = $this->_resultSet[$this->_currentIndex];
            $this->_recno = $this->_currentIndex;
        }
    }

    /**
     * Move by distance.
     */
    public function MoveBy(int $distance): void
    {
        if ($this->_resultSet === null) {
            return;
        }

        $newIndex = $this->_currentIndex + $distance;
        $maxIndex = count($this->_resultSet) - 1;

        if ($newIndex < 0) {
            $newIndex = 0;
        } elseif ($newIndex > $maxIndex) {
            $newIndex = $maxIndex + 1; // EOF position
        }

        $this->_currentIndex = $newIndex;
        $this->_recno = $newIndex;

        if ($newIndex <= $maxIndex) {
            $this->fieldbuffer = $this->_resultSet[$newIndex];
        } else {
            $this->fieldbuffer = [];
        }
    }

    /**
     * Check if at end of file.
     */
    public function EOF(): bool
    {
        return $this->ReadEOF();
    }

    /**
     * Read EOF state.
     */
    public function ReadEOF(): bool
    {
        if ($this->_resultSet === null || empty($this->_resultSet)) {
            return true;
        }
        return $this->_currentIndex >= count($this->_resultSet);
    }

    /**
     * Check if at beginning of file.
     */
    public function BOF(): bool
    {
        return $this->_currentIndex <= 0;
    }

    /**
     * Get fields array.
     */
    public function ReadFields(): array
    {
        return $this->fieldbuffer;
    }

    /**
     * Get field count.
     */
    public function ReadFieldCount(): int
    {
        return count($this->fieldbuffer);
    }

    /**
     * Get record count.
     */
    public function ReadRecordCount(): int
    {
        return $this->_recordcount;
    }

    /**
     * Get all field names.
     */
    public function FieldNames(): array
    {
        return array_keys($this->fieldbuffer);
    }

    /**
     * Get all field values.
     */
    public function FieldValues(): array
    {
        return array_values($this->fieldbuffer);
    }

    /**
     * Get a field value by name.
     */
    public function FieldByName(string $fieldname): mixed
    {
        return $this->fieldbuffer[$fieldname] ?? null;
    }

    /**
     * Move to a specific record by index.
     */
    public function MoveTo(int $index): void
    {
        $this->MoveBy($index - $this->_currentIndex);
    }

    /**
     * Put dataset in edit mode.
     */
    public function Edit(): void
    {
        $this->CallEvent('onbeforeedit', []);
        $this->_originalValues = $this->fieldbuffer;
        $this->_state = DatasetState::Edit;
        $this->CallEvent('onafteredit', []);
    }

    /**
     * Put dataset in insert mode.
     */
    public function Insert(): void
    {
        $this->CallEvent('onbeforeinsert', []);

        // Create empty field buffer
        if ($this->_tablename !== '' && $this->_database !== null) {
            $this->fieldbuffer = $this->_database->MetaFields($this->_tablename);
        } else {
            $this->fieldbuffer = [];
        }

        $this->_state = DatasetState::Insert;
        $this->CallEvent('onafterinsert', []);
    }

    /**
     * Post pending changes.
     */
    public function Post(): void
    {
        $this->CallEvent('onbeforepost', []);
        $this->InternalPost();
        $this->_modified = false;
        $this->_state = DatasetState::Browse;
        $this->CallEvent('onafterpost', []);
    }

    /**
     * Internal post implementation.
     */
    protected function InternalPost(): void
    {
        $this->CheckDatabase();
        $dbal = $this->_database->Dbal();

        $state = $this->_state instanceof DatasetState ? $this->_state : DatasetState::tryFrom($this->_state);

        if ($state === DatasetState::Edit) {
            // UPDATE
            $where = [];
            $buffer = $this->fieldbuffer;

            foreach ($this->_keyfields as $fname) {
                $val = $this->_originalValues[$fname] ?? $this->fieldbuffer[$fname] ?? '';
                unset($buffer[$fname]);
                if (trim((string)$val) !== '') {
                    $where[$fname] = $val;
                }
            }

            if (!empty($where) && !empty($buffer)) {
                $dbal->update($this->_tablename, $buffer, $where);
            }

            // Update result set
            if ($this->_resultSet !== null && isset($this->_resultSet[$this->_currentIndex])) {
                $this->_resultSet[$this->_currentIndex] = $this->fieldbuffer;
            }
        } else {
            // INSERT
            $buffer = $this->fieldbuffer;

            // Remove auto-increment fields
            if ($this->_hasautoinc === '1' && !empty($this->_keyfields)) {
                foreach ($this->_keyfields as $fname) {
                    if (empty($buffer[$fname])) {
                        unset($buffer[$fname]);
                    }
                }
            }

            // Remove empty values
            $buffer = array_filter($buffer, fn($v) => $v !== '' && $v !== null);

            if (!empty($buffer)) {
                $dbal->insert($this->_tablename, $buffer);

                // Get last insert ID for auto-increment
                if ($this->_hasautoinc === '1' && !empty($this->_keyfields)) {
                    $lastId = $this->_database->LastInsertId();
                    if ($lastId > 0) {
                        $this->fieldbuffer[$this->_keyfields[0]] = $lastId;
                    }
                }

                // Add to result set
                if ($this->_resultSet !== null) {
                    $this->_resultSet[] = $this->fieldbuffer;
                    $this->_recordcount = count($this->_resultSet);
                    $this->_currentIndex = $this->_recordcount - 1;
                }
            }
        }

        $this->_originalValues = [];
    }

    /**
     * Cancel pending changes.
     */
    public function Cancel(): void
    {
        $this->CallEvent('onbeforecancel', []);

        if (!empty($this->_originalValues)) {
            $this->fieldbuffer = $this->_originalValues;
            $this->_originalValues = [];
        }

        $this->_modified = false;
        $this->_state = DatasetState::Browse;
        $this->CallEvent('onaftercancel', []);
    }

    /**
     * Delete current record.
     */
    public function Delete(): void
    {
        $this->CallEvent('onbeforedelete', []);

        try {
            $this->InternalDelete();

            // Remove from result set
            if ($this->_resultSet !== null && isset($this->_resultSet[$this->_currentIndex])) {
                array_splice($this->_resultSet, $this->_currentIndex, 1);
                $this->_recordcount = count($this->_resultSet);

                if ($this->_currentIndex >= $this->_recordcount && $this->_recordcount > 0) {
                    $this->_currentIndex = $this->_recordcount - 1;
                }

                if (!empty($this->_resultSet) && isset($this->_resultSet[$this->_currentIndex])) {
                    $this->fieldbuffer = $this->_resultSet[$this->_currentIndex];
                } else {
                    $this->fieldbuffer = [];
                }
            }

            $this->CallEvent('onafterdelete', []);
        } catch (\Exception $e) {
            $this->CallEvent('ondeleteerror', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Internal delete implementation.
     */
    protected function InternalDelete(): void
    {
        $this->CheckDatabase();
        $dbal = $this->_database->Dbal();

        $where = [];
        foreach ($this->_keyfields as $fname) {
            $val = $this->fieldbuffer[$fname] ?? '';
            if (trim((string)$val) !== '') {
                $where[$fname] = $val;
            }
        }

        if (!empty($where)) {
            $dbal->delete($this->_tablename, $where);
        }
    }

    /**
     * Get a field value by name.
     */
    public function FieldGet(string $fieldname): mixed
    {
        if ($this->Active) {
            if (array_key_exists($fieldname, $this->fieldbuffer)) {
                return $this->fieldbuffer[$fieldname];
            }
        }
        throw new \VCL\Core\Exception\PropertyNotFoundException($this->ClassName(), $fieldname);
    }

    /**
     * Set a field value by name.
     */
    public function FieldSet(string $fieldname, mixed $value): void
    {
        if ($this->Active) {
            $this->fieldbuffer[$fieldname] = $value;
            $this->Modified = true;
            if ($this->State === DatasetState::Browse) {
                $this->Edit();
            }
            return;
        }
        throw new \VCL\Core\Exception\PropertyNotFoundException($this->ClassName(), $fieldname);
    }

    /**
     * Magic getter for field access.
     *
     * Note: We must check for database fields FIRST because PHP's method_exists()
     * is case-insensitive. Without this, reading $table->name would incorrectly
     * call Component::getName() instead of reading the database field 'name'.
     */
    public function __get(string $nm): mixed
    {
        // Check if this is a database field first (when table is active)
        if ($this->Active && array_key_exists($nm, $this->fieldbuffer)) {
            return $this->FieldGet($nm);
        }

        try {
            return parent::__get($nm);
        } catch (\VCL\Core\Exception\PropertyNotFoundException $e) {
            return $this->FieldGet($nm);
        }
    }

    /**
     * Magic setter for field access.
     *
     * Note: We must check for database fields FIRST because PHP's method_exists()
     * is case-insensitive. Without this, setting $table->name would incorrectly
     * call Component::setName() instead of setting the database field 'name'.
     */
    public function __set(string $nm, mixed $val): void
    {
        // Check if this is a database field first (when table is active)
        if ($this->Active && array_key_exists($nm, $this->fieldbuffer)) {
            $this->FieldSet($nm, $val);
            return;
        }

        try {
            parent::__set($nm, $val);
        } catch (\VCL\Core\Exception\PropertyNotFoundException $e) {
            $this->FieldSet($nm, $val);
        }
    }

    /**
     * Refresh the dataset.
     */
    public function Refresh(): void
    {
        $wasActive = $this->Active;
        if ($wasActive) {
            $this->Close();
            $this->Open();
        }
    }

    /**
     * Read field properties from dictionary.
     */
    public function ReadFieldProperties(string $fieldname): array
    {
        if ($this->_database !== null) {
            return $this->_database->ReadFieldDictionaryProperties($this->_tablename, $fieldname);
        }
        return [];
    }

    /**
     * Get associative array of field values.
     */
    public function ReadAssociativeFieldValues(): array
    {
        if ($this->Active) {
            return $this->fieldbuffer;
        }
        return [];
    }

    /**
     * Dump hidden fields for form submission.
     */
    public function DumpHiddenKeyFields(string $basename, array $values = []): void
    {
        $keyfields = $this->ReadKeyFields();

        if (empty($values)) {
            $values = $this->ReadAssociativeFieldValues();
        }

        foreach ($keyfields as $v) {
            $avalue = $values[$v] ?? '';
            $avalue = htmlspecialchars((string)$avalue, ENT_QUOTES, 'UTF-8');
            echo "<input type=\"hidden\" name=\"{$basename}[{$v}]\" value=\"{$avalue}\" />";
        }
    }

    // -------------------------------------------------------------------------
    // Legacy Accessors
    // -------------------------------------------------------------------------

    public function getDatabase(): ?Connection { return $this->_database; }
    public function setDatabase(?Connection $value): void { $this->Database = $value; }

    public function getTableName(): string { return $this->_tablename; }
    public function setTableName(string $value): void { $this->TableName = $value; }
    public function defaultTableName(): string { return ''; }

    public function getFilter(): string { return $this->_filter; }
    public function setFilter(string $value): void { $this->Filter = $value; }
    public function defaultFilter(): string { return ''; }

    public function getOrderField(): string { return $this->_orderfield; }
    public function setOrderField(string $value): void { $this->OrderField = $value; }
    public function defaultOrderField(): string { return ''; }

    public function getOrder(): string { return $this->_order; }
    public function setOrder(string $value): void { $this->Order = $value; }
    public function defaultOrder(): string { return 'ASC'; }

    public function getHasAutoInc(): string { return $this->_hasautoinc; }
    public function setHasAutoInc(string $value): void { $this->HasAutoInc = $value; }
    public function defaultHasAutoInc(): string { return '1'; }

    public function getActive(): bool { return $this->Active; }
    public function setActive(bool $value): void { $this->Active = $value; }

    public function getMasterSource(): mixed { return $this->MasterSource; }
    public function setMasterSource(mixed $value): void { $this->MasterSource = $value; }

    public function getMasterFields(): array { return $this->MasterFields; }
    public function setMasterFields(array $value): void { $this->MasterFields = $value; }

    public function getOnBeforeOpen(): ?string { return $this->_onbeforeopen; }
    public function setOnBeforeOpen(?string $value): void { $this->OnBeforeOpen = $value; }

    public function getOnAfterOpen(): ?string { return $this->_onafteropen; }
    public function setOnAfterOpen(?string $value): void { $this->OnAfterOpen = $value; }

    public function getOnBeforeClose(): ?string { return $this->_onbeforeclose; }
    public function setOnBeforeClose(?string $value): void { $this->OnBeforeClose = $value; }

    public function getOnAfterClose(): ?string { return $this->_onafterclose; }
    public function setOnAfterClose(?string $value): void { $this->OnAfterClose = $value; }

    public function getOnBeforeInsert(): ?string { return $this->_onbeforeinsert; }
    public function setOnBeforeInsert(?string $value): void { $this->OnBeforeInsert = $value; }

    public function getOnAfterInsert(): ?string { return $this->_onafterinsert; }
    public function setOnAfterInsert(?string $value): void { $this->OnAfterInsert = $value; }

    public function getOnBeforeEdit(): ?string { return $this->_onbeforeedit; }
    public function setOnBeforeEdit(?string $value): void { $this->OnBeforeEdit = $value; }

    public function getOnAfterEdit(): ?string { return $this->_onafteredit; }
    public function setOnAfterEdit(?string $value): void { $this->OnAfterEdit = $value; }

    public function getOnBeforePost(): ?string { return $this->_onbeforepost; }
    public function setOnBeforePost(?string $value): void { $this->OnBeforePost = $value; }

    public function getOnAfterPost(): ?string { return $this->_onafterpost; }
    public function setOnAfterPost(?string $value): void { $this->OnAfterPost = $value; }

    public function getOnBeforeCancel(): ?string { return $this->_onbeforecancel; }
    public function setOnBeforeCancel(?string $value): void { $this->OnBeforeCancel = $value; }

    public function getOnAfterCancel(): ?string { return $this->_onaftercancel; }
    public function setOnAfterCancel(?string $value): void { $this->OnAfterCancel = $value; }

    public function getOnBeforeDelete(): ?string { return $this->_onbeforedelete; }
    public function setOnBeforeDelete(?string $value): void { $this->OnBeforeDelete = $value; }

    public function getOnAfterDelete(): ?string { return $this->_onafterdelete; }
    public function setOnAfterDelete(?string $value): void { $this->OnAfterDelete = $value; }

    public function getOnDeleteError(): ?string { return $this->_ondeleteerror; }
    public function setOnDeleteError(?string $value): void { $this->OnDeleteError = $value; }
}
