<?php

declare(strict_types=1);

namespace VCL\Database\MySQL;

/**
 * MySQLTable encapsulates a database table in a MySQL server.
 *
 * Use MySQLTable to access data in a single database table using MySQL native access in PHP.
 * Table provides direct access to every record and field in an underlying database table.
 * A table component can also work with a subset of records within a database table using
 * ranges and filters.
 *
 * PHP 8.4 version with Property Hooks.
 */
class MySQLTable extends CustomMySQLTable
{
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

    // Property Hooks for Events
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

    /**
     * Open the dataset.
     */
    public function Open(): void
    {
        $this->callEvent('onbeforeopen', []);
        parent::Open();
        $this->callEvent('onafteropen', []);
    }

    /**
     * Close the dataset.
     */
    public function Close(): void
    {
        $this->callEvent('onbeforeclose', []);
        parent::Close();
        $this->callEvent('onafterclose', []);
    }

    /**
     * Put dataset in insert mode.
     */
    public function Insert(): void
    {
        $this->callEvent('onbeforeinsert', []);
        parent::Insert();
        $this->callEvent('onafterinsert', []);
    }

    /**
     * Put dataset in edit mode.
     */
    public function Edit(): void
    {
        $this->callEvent('onbeforeedit', []);
        parent::Edit();
        $this->callEvent('onafteredit', []);
    }

    /**
     * Post pending changes.
     */
    public function Post(): void
    {
        $this->callEvent('onbeforepost', []);
        parent::Post();
        $this->callEvent('onafterpost', []);
    }

    /**
     * Cancel pending changes.
     */
    public function Cancel(): void
    {
        $this->callEvent('onbeforecancel', []);
        parent::Cancel();
        $this->callEvent('onaftercancel', []);
    }

    /**
     * Delete current record.
     */
    public function Delete(): void
    {
        $this->callEvent('onbeforedelete', []);
        try {
            parent::Delete();
            $this->callEvent('onafterdelete', []);
        } catch (\Exception $e) {
            $this->callEvent('ondeleteerror', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    // Legacy getters/setters for published properties
    public function getMasterSource(): mixed { return $this->readMasterSource(); }
    public function setMasterSource(mixed $value): void { $this->writeMasterSource($value); }

    public function getMasterFields(): array { return $this->readMasterFields(); }
    public function setMasterFields(array $value): void { $this->writeMasterFields($value); }

    public function getTableName(): string { return $this->readTableName(); }
    public function setTableName(string $value): void { $this->writeTableName($value); }

    public function getActive(): bool { return $this->readActive(); }
    public function setActive(bool $value): void { $this->writeActive($value); }

    public function getDatabase(): mixed { return $this->readDatabase(); }
    public function setDatabase(mixed $value): void { $this->writeDatabase($value); }

    public function getFilter(): string { return $this->readFilter(); }
    public function setFilter(string $value): void { $this->writeFilter($value); }

    public function getOrderField(): string { return $this->readOrderField(); }
    public function setOrderField(string $value): void { $this->writeOrderField($value); }

    public function getOrder(): string { return $this->readOrder(); }
    public function setOrder(string $value): void { $this->writeOrder($value); }

    // Event getters/setters
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
