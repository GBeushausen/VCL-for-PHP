<?php

declare(strict_types=1);

namespace VCL\Database;

use VCL\Core\Component;
use VCL\Database\Enums\DatasetState;

/**
 * DataSet is the base class for all dataset components.
 *
 * A DataSet is a collection of information, organized in rows and fields.
 * This class implements the basic interface all data-aware components use
 * to display and manipulate data.
 *
 * PHP 8.4 version with Property Hooks.
 */
class DataSet extends Component
{
    protected int $_limitstart = 0;
    protected int $_limitcount = 10;
    protected int $_recordcount = 0;
    protected DatasetState|int $_state = DatasetState::Inactive;
    protected bool $_modified = false;
    protected bool $_InternalOpenComplete = false;
    protected bool $_DefaultFields = false;
    protected int $_DisableCount = 0;
    protected ?Field $_datasetfield = null;
    protected mixed $_mastersource = null;
    protected array $_masterfields = [];
    protected int $_recno = 0;
    protected array $_reckey = [];
    protected bool $_canmodify = true;

    public array $fieldbuffer = [];

    // Property Hooks
    public int $LimitStart {
        get => $this->_limitstart;
        set => $this->_limitstart = max(0, $value);
    }

    public int $LimitCount {
        get => $this->_limitcount;
        set => $this->_limitcount = max(1, $value);
    }

    public DatasetState|int $State {
        get => $this->_state;
        set => $this->_state = $value;
    }

    public bool $Modified {
        get => $this->_modified;
        set => $this->_modified = $value;
    }

    public bool $CanModify {
        get => $this->_canmodify;
        set => $this->_canmodify = $value;
    }

    public ?Field $DataSetField {
        get => $this->_datasetfield;
        set => $this->_datasetfield = $value;
    }

    public mixed $MasterSource {
        get => $this->_mastersource;
        set => $this->_mastersource = $this->fixupProperty($value);
    }

    public array $MasterFields {
        get => $this->_masterfields;
        set => $this->_masterfields = $value;
    }

    public int $RecNo {
        get => $this->_recno;
        set {
            if ($value !== $this->_recno) {
                $diff = $value - $this->_recno;
                if ($diff > 0) {
                    $this->MoveBy($diff);
                }
                $this->_recno = $value;
            }
        }
    }

    public array $RecKey {
        get => $this->_reckey;
        set => $this->_reckey = $value;
    }

    protected bool $_fstreamedactive = false;

    public bool $Active {
        get => $this->readActive();
        set { $this->writeActive($value); }
    }

    /**
     * Read active state.
     */
    public function readActive(): bool
    {
        $state = $this->_state instanceof DatasetState
            ? $this->_state
            : DatasetState::tryFrom($this->_state);

        if ($state === DatasetState::Inactive || $state === DatasetState::Opening) {
            return false;
        }
        return true;
    }

    /**
     * Write active state.
     */
    public function writeActive(bool $value): void
    {
        if (($this->ControlState & CS_LOADING) === CS_LOADING) {
            $this->_fstreamedactive = $value;
            return;
        }

        if ($this->readActive() !== $value) {
            if ($value) {
                $this->Open();
            } else {
                $this->Close();
            }
        }
    }

    /**
     * Called when component is loaded.
     */
    public function loaded(): void
    {
        parent::loaded();
        $this->MasterSource = $this->_mastersource;
        if ($this->_fstreamedactive) {
            $this->Active = true;
        }
    }

    /**
     * Internal close implementation.
     */
    public function internalClose(): void
    {
        // Override in subclass
    }

    /**
     * Internal exception handler.
     */
    public function internalHandleException(): void
    {
        // Override in subclass
    }

    /**
     * Initialize field definitions.
     */
    public function internalInitFieldDefs(): void
    {
        // Override in subclass
    }

    /**
     * Internal open implementation.
     */
    public function internalOpen(): void
    {
        // Override in subclass
    }

    /**
     * Check if cursor is open.
     */
    public function isCursorOpen(): bool
    {
        return false;
    }

    /**
     * Get fields array.
     */
    public function readFields(): array
    {
        return [];
    }

    /**
     * Get field count.
     */
    public function readFieldCount(): int
    {
        return 0;
    }

    /**
     * Get record count.
     */
    public function readRecordCount(): int
    {
        return $this->_recordcount;
    }

    /**
     * Handle data events.
     */
    public function DataEvent(int $event, mixed $info = null): void
    {
        // Override in subclass
    }

    /**
     * Move cursor by specified amount.
     */
    public function MoveBy(int $distance): void
    {
        // Override in subclass
    }

    /**
     * Move to first record.
     */
    public function First(): void
    {
        // Override in subclass
    }

    /**
     * Move to last record.
     */
    public function Last(): void
    {
        // Override in subclass
    }

    /**
     * Move to next record.
     */
    public function Next(): void
    {
        $this->MoveBy(1);
    }

    /**
     * Move to previous record.
     */
    public function Prior(): void
    {
        $this->MoveBy(-1);
    }

    /**
     * Check if at end of file.
     */
    public function EOF(): bool
    {
        return false;
    }

    /**
     * Check if at beginning of file.
     */
    public function BOF(): bool
    {
        return false;
    }

    /**
     * Open the dataset.
     */
    public function Open(): void
    {
        $this->internalOpen();
        $this->_state = DatasetState::Browse;
    }

    /**
     * Close the dataset.
     */
    public function Close(): void
    {
        $this->internalClose();
        $this->_state = DatasetState::Inactive;
    }

    /**
     * Put dataset in edit mode.
     */
    public function Edit(): void
    {
        $this->_state = DatasetState::Edit;
    }

    /**
     * Put dataset in insert mode.
     */
    public function Insert(): void
    {
        $this->_state = DatasetState::Insert;
    }

    /**
     * Post pending changes.
     */
    public function Post(): void
    {
        $this->_modified = false;
        $this->_state = DatasetState::Browse;
    }

    /**
     * Cancel pending changes.
     */
    public function Cancel(): void
    {
        $this->_modified = false;
        $this->_state = DatasetState::Browse;
    }

    /**
     * Delete current record.
     */
    public function Delete(): void
    {
        // Override in subclass
    }

    /**
     * Refresh the dataset.
     */
    public function Refresh(): void
    {
        // Override in subclass
    }

    /**
     * Check if dataset is active.
     */
    public function isActive(): bool
    {
        $state = $this->_state instanceof DatasetState
            ? $this->_state
            : DatasetState::from($this->_state);

        return $state !== DatasetState::Inactive;
    }

    /**
     * Serialize state.
     */
    public function serialize(): void
    {
        parent::serialize();

        $owner = $this->readOwner();
        if ($owner !== null) {
            $prefix = $owner->readNamePath() . '.' . $this->_name . '.';
            $_SESSION[$prefix . 'State'] = $this->_state instanceof DatasetState
                ? $this->_state->value
                : $this->_state;
        }
    }

    /**
     * Unserialize state.
     */
    public function unserialize(): void
    {
        parent::unserialize();

        $owner = $this->readOwner();
        if ($owner !== null) {
            $prefix = $owner->readNamePath() . '.' . $this->_name . '.';
            if (isset($_SESSION[$prefix . 'State'])) {
                $stateValue = $_SESSION[$prefix . 'State'];
                $this->_state = is_int($stateValue)
                    ? DatasetState::tryFrom($stateValue) ?? $stateValue
                    : $stateValue;
            }
        }
    }

    // Legacy getters/setters
    public function getLimitStart(): int { return $this->_limitstart; }
    public function setLimitStart(int $value): void { $this->LimitStart = $value; }
    public function defaultLimitStart(): int { return 0; }

    public function getLimitCount(): int { return $this->_limitcount; }
    public function setLimitCount(int $value): void { $this->LimitCount = $value; }
    public function defaultLimitCount(): int { return 10; }

    public function readState(): DatasetState|int { return $this->_state; }
    public function writeState(DatasetState|int $value): void { $this->State = $value; }
    public function defaultState(): int { return dsInactive; }

    public function readModified(): bool { return $this->_modified; }
    public function writeModified(bool $value): void { $this->Modified = $value; }
    public function defaultModified(): bool { return false; }

    public function readCanModify(): bool { return $this->_canmodify; }
    public function writeCanModify(bool $value): void { $this->CanModify = $value; }

    public function readDataSetField(): ?Field { return $this->_datasetfield; }
    public function writeDataSetField(?Field $value): void { $this->DataSetField = $value; }
    public function defaultDataSetField(): ?Field { return null; }

    public function readMasterSource(): mixed { return $this->_mastersource; }
    public function writeMasterSource(mixed $value): void { $this->MasterSource = $value; }

    public function readMasterFields(): array { return $this->_masterfields; }
    public function writeMasterFields(array $value): void { $this->MasterFields = $value; }
    public function defaultMasterFields(): array { return []; }

    public function readRecNo(): int { return $this->_recno; }
    public function writeRecNo(int $value): void { $this->RecNo = $value; }
    public function defaultRecNo(): int { return 0; }

    public function readRecKey(): array { return $this->_reckey; }
    public function writeRecKey(array $value): void { $this->RecKey = $value; }
    public function defaultRecKey(): array { return []; }
}
