<?php

declare(strict_types=1);

namespace VCL\Database;

use VCL\Core\Component;
use VCL\Core\Collection;

/**
 * CustomConnection is the base class for all database connection components.
 *
 * A connection must represent the object through which data objects
 * (tables, queries, etc.) perform their operations. Components like
 * Database inherit from CustomConnection and implement specific functionality.
 *
 * PHP 8.4 version with Property Hooks.
 */
class CustomConnection extends Component
{
    protected ?Collection $_datasets = null;
    protected bool $_fstreamedconnected = false;
    protected ?Collection $_clients = null;

    // Events
    protected ?string $_onafterconnect = null;
    protected ?string $_onbeforeconnect = null;
    protected ?string $_oncustomconnect = null;
    protected ?string $_onafterdisconnect = null;
    protected ?string $_onbeforedisconnect = null;
    protected ?string $_onlogin = null;

    // Property Hooks
    public ?Collection $DataSets {
        get => $this->_datasets;
        set => $this->_datasets = $value;
    }

    public ?Collection $Clients {
        get => $this->_clients;
        set => $this->_clients = $value;
    }

    public bool $Connected {
        get => $this->readConnected();
        set => $this->writeConnected($value);
    }

    public ?string $OnAfterConnect {
        get => $this->_onafterconnect;
        set => $this->_onafterconnect = $value;
    }

    public ?string $OnBeforeConnect {
        get => $this->_onbeforeconnect;
        set => $this->_onbeforeconnect = $value;
    }

    public ?string $OnCustomConnect {
        get => $this->_oncustomconnect;
        set => $this->_oncustomconnect = $value;
    }

    public ?string $OnAfterDisconnect {
        get => $this->_onafterdisconnect;
        set => $this->_onafterdisconnect = $value;
    }

    public ?string $OnBeforeDisconnect {
        get => $this->_onbeforedisconnect;
        set => $this->_onbeforedisconnect = $value;
    }

    public ?string $OnLogin {
        get => $this->_onlogin;
        set => $this->_onlogin = $value;
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->_datasets = new Collection();
        $this->_clients = new Collection();
    }

    /**
     * Returns the field names for a table.
     */
    public function MetaFields(string $tablename): array
    {
        return [];
    }

    /**
     * Begin a new transaction.
     */
    public function BeginTrans(): void
    {
        // Override in subclass
    }

    /**
     * Complete the current transaction.
     */
    public function CompleteTrans(bool $autocomplete = true): bool
    {
        // Override in subclass
        return true;
    }

    /**
     * Send connect event to all datasets.
     */
    public function SendConnectEvent(bool $connecting): void
    {
        if ($this->_clients === null) {
            return;
        }

        for ($i = 0; $i < $this->_clients->count(); $i++) {
            $client = $this->_clients->items[$i];
            if ($client->inheritsFrom('DataSet')) {
                $client->DataEvent(deConnectChange, $connecting);
            }
        }
    }

    /**
     * Format a date for this database.
     */
    public function DBDate(string $input): string
    {
        return $input;
    }

    /**
     * Prepare a query for execution.
     */
    public function Prepare(string $query): void
    {
        // Override in subclass
    }

    /**
     * Format a parameter for this database.
     */
    public function Param(string $input): string
    {
        return $input;
    }

    /**
     * Quote a string for this database.
     */
    public function QuoteStr(string $input): string
    {
        return $input;
    }

    /**
     * Check if connected.
     */
    protected function readConnected(): bool
    {
        return false;
    }

    /**
     * Set connected state.
     */
    protected function writeConnected(bool $value): void
    {
        if (($this->ControlState & csLoading) === csLoading) {
            $this->_fstreamedconnected = $value;
            return;
        }

        if ($value === $this->readConnected()) {
            return;
        }

        if ($value) {
            $this->callEvent('onbeforeconnect', []);
            $this->DoConnect();
            $this->SendConnectEvent(true);
            $this->callEvent('onafterconnect', []);
        } else {
            $this->callEvent('onbeforedisconnect', []);
            $this->SendConnectEvent(false);
            $this->DoDisconnect();
            $this->callEvent('onafterdisconnect', []);
        }
    }

    /**
     * Open the connection.
     */
    public function Open(): void
    {
        $this->Connected = true;
    }

    /**
     * Close the connection.
     */
    public function Close(): void
    {
        $this->Connected = false;
    }

    /**
     * Connect implementation (override in subclass).
     */
    protected function DoConnect(): void
    {
        // Override in subclass
    }

    /**
     * Disconnect implementation (override in subclass).
     */
    protected function DoDisconnect(): void
    {
        // Override in subclass
    }

    // Legacy getters/setters
    public function readDataSets(): ?Collection { return $this->_datasets; }
    public function writeDataSets(?Collection $value): void { $this->DataSets = $value; }
    public function defaultDataSets(): ?Collection { return null; }

    public function readClients(): ?Collection { return $this->_clients; }
    public function writeClients(?Collection $value): void { $this->Clients = $value; }
    public function defaultClients(): ?Collection { return null; }

    public function readOnAfterConnect(): ?string { return $this->_onafterconnect; }
    public function writeOnAfterConnect(?string $value): void { $this->OnAfterConnect = $value; }
    public function defaultOnAfterConnect(): ?string { return null; }

    public function readOnBeforeConnect(): ?string { return $this->_onbeforeconnect; }
    public function writeOnBeforeConnect(?string $value): void { $this->OnBeforeConnect = $value; }
    public function defaultOnBeforeConnect(): ?string { return null; }

    public function getOnCustomConnect(): ?string { return $this->_oncustomconnect; }
    public function setOnCustomConnect(?string $value): void { $this->OnCustomConnect = $value; }
    public function defaultOnCustomConnect(): ?string { return null; }

    public function readOnAfterDisconnect(): ?string { return $this->_onafterdisconnect; }
    public function writeOnAfterDisconnect(?string $value): void { $this->OnAfterDisconnect = $value; }
    public function defaultOnAfterDisconnect(): ?string { return null; }

    public function readOnBeforeDisconnect(): ?string { return $this->_onbeforedisconnect; }
    public function writeOnBeforeDisconnect(?string $value): void { $this->OnBeforeDisconnect = $value; }
    public function defaultOnBeforeDisconnect(): ?string { return null; }

    public function defaultConnected(): bool { return false; }
}
