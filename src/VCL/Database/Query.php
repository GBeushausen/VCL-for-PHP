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

use Doctrine\DBAL\Result;
use VCL\Database\Enums\DatasetState;

/**
 * Query represents a dataset with a result set based on an SQL statement.
 *
 * Use Query to access one or more tables in a database using SQL statements.
 * Query uses Doctrine DBAL for database operations.
 *
 * Example:
 * ```php
 * $query = new Query();
 * $query->Database = $connection;
 * $query->SQL = ['SELECT * FROM users WHERE status = ?'];
 * $query->Params = ['active'];
 * $query->Active = true;
 *
 * while (!$query->EOF) {
 *     echo $query->username . "\n";
 *     $query->Next();
 * }
 * ```
 */
class Query extends DataSet
{
    protected ?Connection $_database = null;
    protected array $_sql = [];
    protected array $_params = [];
    protected array $_paramTypes = [];
    protected ?array $_resultSet = null;
    protected int $_currentIndex = 0;
    protected string $_filter = '';
    protected string $_orderfield = '';
    protected string $_order = 'ASC';
    protected array $_keyfields = [];

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

    public array $SQL {
        get => $this->_sql;
        set => $this->_sql = $value;
    }

    public array $Params {
        get => $this->_params;
        set => $this->_params = $value;
    }

    public array $ParamTypes {
        get => $this->_paramTypes;
        set => $this->_paramTypes = $value;
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

        $sql = !empty($this->_sql) ? implode(' ', $this->_sql) : '';

        if ($this->_filter !== '' && stripos($sql, 'WHERE') === false) {
            $sql .= " WHERE {$this->_filter}";
        } elseif ($this->_filter !== '') {
            $sql .= " AND ({$this->_filter})";
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
     * Open the query and fetch results.
     */
    public function InternalOpen(): void
    {
        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            return;
        }

        $sql = $this->BuildQuery();
        if (trim($sql) === '') {
            throw new EDatabaseError("Missing SQL query to execute");
        }

        $this->CheckDatabase();
        $this->_database->Open();

        $limitstart = $this->LimitStart;
        $limitcount = $this->LimitCount;

        if ($limitstart === 0 && $limitcount === 0) {
            $result = $this->_database->Execute($sql, $this->_params, $this->_paramTypes);
        } else {
            $result = $this->_database->ExecuteLimit($sql, $limitcount, $limitstart, $this->_params, $this->_paramTypes);
        }

        $this->_resultSet = $result->fetchAllAssociative();
        $this->_currentIndex = 0;
        $this->_recordcount = count($this->_resultSet);

        if (!empty($this->_resultSet)) {
            $this->fieldbuffer = $this->_resultSet[0];
        } else {
            $this->fieldbuffer = [];
        }
    }

    /**
     * Close the query.
     */
    public function InternalClose(): void
    {
        $this->_resultSet = null;
        $this->_currentIndex = 0;
        $this->_recordcount = 0;
        $this->fieldbuffer = [];
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
                $this->State = DatasetState::Edit;
            }
            return;
        }
        throw new \VCL\Core\Exception\PropertyNotFoundException($this->ClassName(), $fieldname);
    }

    /**
     * Magic getter for field access.
     */
    public function __get(string $nm): mixed
    {
        try {
            return parent::__get($nm);
        } catch (\VCL\Core\Exception\PropertyNotFoundException $e) {
            return $this->FieldGet($nm);
        }
    }

    /**
     * Magic setter for field access.
     */
    public function __set(string $nm, mixed $val): void
    {
        try {
            parent::__set($nm, $val);
        } catch (\VCL\Core\Exception\PropertyNotFoundException $e) {
            $this->FieldSet($nm, $val);
        }
    }

    /**
     * Execute the query and return all results.
     */
    public function FetchAll(): array
    {
        if (!$this->Active) {
            $this->Open();
        }
        return $this->_resultSet ?? [];
    }

    /**
     * Execute the query and return first row.
     */
    public function FetchOne(): array|false
    {
        if (!$this->Active) {
            $this->Open();
        }
        return $this->_resultSet[0] ?? false;
    }

    /**
     * Execute a non-SELECT statement.
     */
    public function ExecSQL(): int
    {
        $this->CheckDatabase();
        $this->_database->Open();

        $sql = $this->BuildQuery();
        return $this->_database->ExecuteStatement($sql, $this->_params, $this->_paramTypes);
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
     * Prepare the query (for compatibility).
     */
    public function Prepare(): void
    {
        // DBAL uses prepared statements by default
    }

    // -------------------------------------------------------------------------
    // Legacy Accessors
    // -------------------------------------------------------------------------

    public function getDatabase(): ?Connection { return $this->_database; }
    public function setDatabase(?Connection $value): void { $this->Database = $value; }

    public function getSQL(): array { return $this->_sql; }
    public function setSQL(array|string $value): void { $this->SQL = is_array($value) ? $value : [$value]; }
    public function defaultSQL(): array { return []; }

    public function getParams(): array { return $this->_params; }
    public function setParams(array $value): void { $this->Params = $value; }
    public function defaultParams(): string { return ''; }

    public function getFilter(): string { return $this->_filter; }
    public function setFilter(string $value): void { $this->Filter = $value; }
    public function defaultFilter(): string { return ''; }

    public function getOrderField(): string { return $this->_orderfield; }
    public function setOrderField(string $value): void { $this->OrderField = $value; }
    public function defaultOrderField(): string { return ''; }

    public function getOrder(): string { return $this->_order; }
    public function setOrder(string $value): void { $this->Order = $value; }
    public function defaultOrder(): string { return 'ASC'; }

    public function getActive(): bool { return $this->Active; }
    public function setActive(bool $value): void { $this->Active = $value; }

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
