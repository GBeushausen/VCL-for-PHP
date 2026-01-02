<?php
/**
 * VCL for PHP
 *
 * Copyright (c) 2004-2008 qadram software S.L.
 * Copyright (c) 2026 Gunnar Beushausen
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 */

declare(strict_types=1);

namespace VCL\Actions;

use VCL\Core\Component;

/**
 * A list of actions for processing web requests.
 *
 * ActionList provides URL-based action routing for VCL applications.
 * When a web request contains a parameter named like the component name
 * and the value matches an action in the Actions array, the OnExecute
 * event is fired.
 *
 * Example URL: http://localhost/unit1.php?ActionList1=showmessage
 *
 * Example usage:
 * ```php
 * $actionList = new ActionList($this);
 * $actionList->Name = 'ActionList1';
 * $actionList->Actions = ['showmessage', 'deletemessage', 'editmessage'];
 * $actionList->OnExecute = 'ActionList1Execute';
 *
 * // In your event handler:
 * public function ActionList1Execute(object $sender, array $params): void
 * {
 *     match ($params['action']) {
 *         'showmessage' => $this->showMessage(),
 *         'deletemessage' => $this->deleteMessage(),
 *         'editmessage' => $this->editMessage(),
 *         default => null,
 *     };
 * }
 * ```
 */
class ActionList extends Component
{
    protected array $_actions = [];
    protected ?string $_onexecute = null;

    // =========================================================================
    // PROPERTY HOOKS
    // =========================================================================

    /**
     * Array holding all the actions in the list.
     */
    public array $Actions {
        get => $this->_actions;
        set => $this->_actions = $value;
    }

    /**
     * Event fired when a matching action is found in the request.
     */
    public ?string $OnExecute {
        get => $this->_onexecute;
        set => $this->_onexecute = $value;
    }

    // =========================================================================
    // CONSTRUCTOR
    // =========================================================================

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
    }

    // =========================================================================
    // LIFECYCLE METHODS
    // =========================================================================

    public function init(): void
    {
        parent::init();

        $action = $this->input->{$this->_name} ?? null;
        if (is_object($action)) {
            $this->executeAction($action->asString());
        }
    }

    // =========================================================================
    // PUBLIC METHODS
    // =========================================================================

    /**
     * Adds a new action to the Actions array.
     *
     * @param string $action Name of the action to be added
     */
    public function addAction(string $action): void
    {
        $this->_actions[] = $action;
    }

    /**
     * Deletes an action from the Actions array.
     *
     * @param string $action Name of the action to be deleted
     */
    public function deleteAction(string $action): void
    {
        $key = array_search($action, $this->_actions);
        if ($key !== false) {
            array_splice($this->_actions, $key, 1);
        }
    }

    /**
     * Check if an action exists in the Actions array.
     *
     * @param string $action Name of the action to check
     * @return bool True if the action exists
     */
    public function hasAction(string $action): bool
    {
        return in_array($action, $this->_actions, true);
    }

    /**
     * Forces a call to the OnExecute event, if attached and if the action exists.
     *
     * @param string $action Name of the action to execute
     * @return mixed Returns the value returned by the event handler, or false if not handled
     */
    public function executeAction(string $action): mixed
    {
        if ($this->_onexecute !== null && in_array($action, $this->_actions, true)) {
            return $this->callEvent('onexecute', ['action' => $action]);
        }
        return false;
    }

    /**
     * Adds an action parameter to a URL.
     *
     * Use this method to generate URLs that will trigger specific actions.
     *
     * @param string $action Name of the action to add
     * @param string &$url The URL to modify
     * @return bool True if the action was successfully added
     */
    public function expandActionToURL(string $action, string &$url): bool
    {
        if (!in_array($action, $this->_actions, true)) {
            return false;
        }

        // Check if query string has started
        $url .= (strpos($url, '?') === false) ? '?' : '&';
        $url .= urlencode($this->_name) . '=' . urlencode($action);

        return true;
    }

    /**
     * Generate a complete URL for an action.
     *
     * @param string $action Name of the action
     * @param string $baseUrl Optional base URL (defaults to current script)
     * @return string|null The complete URL, or null if action doesn't exist
     */
    public function getActionURL(string $action, string $baseUrl = ''): ?string
    {
        if (!in_array($action, $this->_actions, true)) {
            return null;
        }

        if ($baseUrl === '') {
            $baseUrl = $_SERVER['PHP_SELF'] ?? '';
        }

        $this->expandActionToURL($action, $baseUrl);
        return $baseUrl;
    }

    // =========================================================================
    // DEFAULT VALUE METHODS
    // =========================================================================

    protected function defaultActions(): array
    {
        return [];
    }

    protected function defaultOnExecute(): ?string
    {
        return null;
    }
}
