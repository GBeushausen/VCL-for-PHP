<?php

declare(strict_types=1);

namespace VCL\Database\MySQL;

use VCL\Database\DataSet;
use VCL\Database\EDatabaseError;
use VCL\Database\Enums\DatasetState;
use VCL\Core\Exception\PropertyNotFoundException;
use mysqli_result;

/**
 * MySQLDataSet encapsulates database connectivity for descendant dataset objects.
 *
 * MySQLDataSet defines database-related connectivity properties and methods for a dataset.
 * Applications never use MySQLDataSet objects directly. Instead they use the descendants of MySQLDataSet,
 * such as MySQLQuery, MySQLStoredProc, and MySQLTable, which inherit its database-related properties and methods.
 *
 * PHP 8.4 version with Property Hooks.
 */
class MySQLDataSet extends DataSet
{
    public ?mysqli_result $_rs = null;
    protected bool $_eof = false;
    public array $_buffer = [];
    protected mixed $_database = null;
    protected array $_params = [];
    public array $_keyfields = [];
    protected string $_tablename = '';
    protected string $_lastquery = '';
    protected string $_filter = '';

    // Property Hooks
    public mixed $Database {
        get => $this->_database;
        set => $this->_database = $this->fixupProperty($value);
    }

    public array $Params {
        get => $this->_params;
        set => $this->_params = $value;
    }

    public string $Filter {
        get => $this->_filter;
        set => $this->_filter = $value;
    }

    /**
     * Called when component is loaded.
     */
    public function loaded(): void
    {
        $this->Database = $this->_database;
        parent::loaded();
    }

    /**
     * Get fields array.
     */
    public function readFields(): array
    {
        return $this->_buffer;
    }

    /**
     * Get field count.
     */
    public function readFieldCount(): int
    {
        return count($this->_buffer);
    }

    /**
     * Get record count.
     */
    public function readRecordCount(): int
    {
        if ($this->_rs !== null) {
            return mysqli_num_rows($this->_rs);
        }
        return parent::readRecordCount();
    }

    /**
     * Move cursor by specified amount.
     */
    public function MoveBy(int $distance): void
    {
        parent::MoveBy($distance);

        $buff = null;
        for ($i = 0; $i <= $distance - 1; $i++) {
            $buff = mysqli_fetch_assoc($this->_rs);
            $this->_eof = ($buff === null || $buff === false);
        }

        if (!$this->_eof && $buff !== null && $buff !== false) {
            $this->_buffer = $buff;
        }
    }

    /**
     * Check if at end of file.
     */
    public function EOF(): bool
    {
        return $this->_eof;
    }

    /**
     * Check if the Database property is assigned and is an object.
     */
    public function CheckDatabase(): void
    {
        if (!is_object($this->_database)) {
            throw new EDatabaseError("No Database assigned or is not an object");
        }
    }

    /**
     * Check if dataset is active.
     */
    public function readActive(): bool
    {
        return $this->isActive();
    }

    /**
     * Internal open implementation.
     */
    public function internalOpen(?string $lquery = null): void
    {
        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            return;
        }

        $query = $lquery ?? $this->buildQuery();
        if (trim($query) === '') {
            throw new EDatabaseError("Missing query to execute");
        }

        $this->CheckDatabase();
        $this->_eof = false;

        $limitstart = $this->_limitstart;
        $limitcount = $this->_limitcount;

        if ($limitstart === -1 && $limitcount === -1) {
            $this->_rs = $this->_database->Execute($query, $this->_params);
            $this->_buffer = [];
            $this->MoveBy(1);
        } else {
            if ($limitstart < 0) {
                $limitstart = 0;
            }
            if ($limitcount < 0) {
                $limitcount = 10;
            }

            $this->_rs = $this->_database->ExecuteLimit($query, $limitcount, $limitstart, $this->_params);
            $this->_buffer = [];
            $this->MoveBy(1);
        }

        if (count($this->_buffer) === 0) {
            if ($this->_tablename !== '') {
                $fd = $this->_database->MetaFields($this->_tablename);
                $this->_buffer = $fd;
            }
        }

        $this->_keyfields = $this->readKeyFields();
        $this->fieldbuffer = $this->_buffer;
        $this->_lastquery = $query;
    }

    /**
     * Internal close implementation.
     */
    public function internalClose(): void
    {
        if ($this->_rs !== null) {
            mysqli_free_result($this->_rs);
            $this->_rs = null;
        }
        $this->_buffer = [];
        $this->_eof = false;
    }

    /**
     * Move to first record.
     */
    public function First(): void
    {
        $this->internalClose();
        $this->internalOpen($this->_lastquery);
    }

    /**
     * Build the query to send to the server.
     */
    protected function buildQuery(): string
    {
        return '';
    }

    /**
     * Read key fields for the table.
     */
    public function readKeyFields(): array
    {
        return [];
    }

    /**
     * Returns the value of a field on the dataset.
     *
     * @throws PropertyNotFoundException
     */
    public function fieldget(string $fieldname): mixed
    {
        if ($this->_rs !== null) {
            if ($this->readActive()) {
                if (array_key_exists($fieldname, $this->_buffer)) {
                    $state = $this->_state instanceof DatasetState
                        ? $this->_state
                        : DatasetState::tryFrom($this->_state);

                    if ($state === DatasetState::Browse) {
                        return $this->_buffer[$fieldname];
                    } elseif (array_key_exists($fieldname, $this->fieldbuffer)) {
                        return $this->fieldbuffer[$fieldname];
                    }
                    return '';
                } elseif (array_key_exists($fieldname, $this->fieldbuffer)) {
                    return $this->fieldbuffer[$fieldname];
                }
            }
        }

        throw new PropertyNotFoundException($this->ClassName(), $fieldname);
    }

    /**
     * Sets the value of a field on the dataset.
     *
     * @throws PropertyNotFoundException
     */
    public function fieldset(string $fieldname, mixed $value): void
    {
        if ($this->_rs !== null) {
            if ($this->readActive()) {
                if (array_key_exists($fieldname, $this->_buffer)) {
                    $this->fieldbuffer[$fieldname] = $value;
                    $this->Modified = true;

                    $state = $this->_state instanceof DatasetState
                        ? $this->_state
                        : DatasetState::tryFrom($this->_state);

                    if ($state === DatasetState::Browse) {
                        $this->State = DatasetState::Edit;
                    }
                    return;
                }
            }
        }

        throw new PropertyNotFoundException($this->ClassName(), $fieldname);
    }

    /**
     * Overridden to allow get field values as properties.
     */
    public function __get(string $nm): mixed
    {
        try {
            return parent::__get($nm);
        } catch (PropertyNotFoundException $e) {
            if ($this->_rs !== null && $this->readActive()) {
                if (array_key_exists($nm, $this->_buffer)) {
                    $state = $this->_state instanceof DatasetState
                        ? $this->_state
                        : DatasetState::tryFrom($this->_state);

                    if ($state === DatasetState::Browse) {
                        return $this->_buffer[$nm];
                    } elseif (array_key_exists($nm, $this->fieldbuffer)) {
                        return $this->fieldbuffer[$nm];
                    }
                    return '';
                } elseif (array_key_exists($nm, $this->fieldbuffer)) {
                    return $this->fieldbuffer[$nm];
                }
            }
            throw $e;
        }
    }

    /**
     * Overridden to allow set field values as properties.
     */
    public function __set(string $nm, mixed $val): void
    {
        try {
            parent::__set($nm, $val);
        } catch (PropertyNotFoundException $e) {
            if ($this->_rs !== null && $this->readActive()) {
                if (array_key_exists($nm, $this->_buffer)) {
                    $this->fieldbuffer[$nm] = $val;
                    $this->Modified = true;

                    $state = $this->_state instanceof DatasetState
                        ? $this->_state
                        : DatasetState::tryFrom($this->_state);

                    if ($state === DatasetState::Browse) {
                        $this->State = DatasetState::Edit;
                    }
                    return;
                }
            }
            throw $e;
        }
    }

    // Legacy getters/setters
    public function readDatabase(): mixed { return $this->_database; }
    public function writeDatabase(mixed $value): void { $this->Database = $value; }
    public function defaultDatabase(): mixed { return null; }

    public function readParams(): array { return $this->_params; }
    public function writeParams(array $value): void { $this->Params = $value; }
    public function defaultParams(): array { return []; }

    public function readFilter(): string { return $this->_filter; }
    public function writeFilter(string $value): void { $this->Filter = $value; }
    public function defaultFilter(): string { return ''; }

    public function readEOF(): bool { return $this->_eof; }
}
