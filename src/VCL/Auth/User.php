<?php

declare(strict_types=1);

namespace VCL\Auth;

use VCL\Core\Component;

/**
 * A common base class for user authentication.
 *
 * Inherit from this class to create new types of authentication.
 *
 * The basic usage is, call the Authenticate method with the username/password
 * combination and then, check for the Logged property to know if the operation
 * has been successful or not.
 *
 * PHP 8.4 version with Property Hooks.
 */
class User extends Component
{
    protected bool $_logged = false;

    // Property Hooks
    public bool $Logged {
        get => $this->_logged;
        set => $this->_logged = $value;
    }

    /**
     * Authenticate the user in the system with the specified username and password.
     *
     * This method should update the Logged property of the component
     * to let you know about the success of the authentication.
     *
     * @param string $username Name of the user to authenticate
     * @param string $password Password of the user to authenticate
     * @return bool True if authentication was successful
     */
    public function authenticate(string $username, string $password): bool
    {
        // Base implementation - override in subclasses
        return false;
    }

    // Legacy getters/setters
    public function readLogged(): bool { return $this->_logged; }
    public function writeLogged(bool $value): void { $this->Logged = $value; }
}
