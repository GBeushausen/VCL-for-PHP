<?php

declare(strict_types=1);

namespace VCL\Auth;

use VCL\Core\Component;

/**
 * Performs authentication using HTTP Basic Authentication.
 *
 * This component is useful for protecting web pages easily by just dropping a component.
 * For basic usage, just set UserName and Password to the valid value to log in
 * and call the Execute() method in the OnBeforeShow event of your page.
 *
 * For more advanced usage, the OnAuthenticate event allows you to authenticate using your
 * own rules.
 *
 * @link http://www.w3.org/Protocols/HTTP/1.0/draft-ietf-http-spec.html#Code401
 *
 * PHP 8.4 version with Property Hooks.
 */
class BasicAuthentication extends Component
{
    protected string $_title = 'Login';
    protected string $_errormessage = 'Unauthorized';
    protected string $_username = '';
    protected string $_password = '';
    protected ?string $_onauthenticate = null;

    // Property Hooks
    public string $Title {
        get => $this->_title;
        set => $this->_title = $value;
    }

    public string $ErrorMessage {
        get => $this->_errormessage;
        set => $this->_errormessage = $value;
    }

    public string $Username {
        get => $this->_username;
        set => $this->_username = $value;
    }

    public string $Password {
        get => $this->_password;
        set => $this->_password = $value;
    }

    public ?string $OnAuthenticate {
        get => $this->_onauthenticate;
        set => $this->_onauthenticate = $value;
    }

    /**
     * Executes the authentication and checks if the user has been authenticated or not.
     *
     * This method tries to perform the user authentication. If the user has not been
     * already authenticated, it requests the username/password using a browser dialog.
     * If the user has been authenticated, it does nothing.
     *
     * If the event OnAuthenticate is assigned, the valid username/password will be
     * provided by code. If not, the Username/Password properties will be used to
     * authenticate.
     *
     * @return bool True if authentication was successful
     */
    public function Execute(): bool
    {
        $result = false;

        // If authorization not set, request it
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            $this->sendAuthHeaders();
            die($this->_errormessage);
        }

        $providedUsername = $_SERVER['PHP_AUTH_USER'];
        $providedPassword = $_SERVER['PHP_AUTH_PW'] ?? '';

        // If OnAuthenticate event is assigned, use it
        if ($this->_onauthenticate !== null) {
            $result = $this->callEvent('onauthenticate', [
                'username' => $providedUsername,
                'password' => $providedPassword,
            ]);

            if (!$result) {
                $this->sendAuthHeaders();
                die($this->_errormessage);
            }
        } else {
            // Check against Username/Password properties
            if ($providedUsername !== $this->_username || $providedPassword !== $this->_password) {
                $this->sendAuthHeaders();
                die($this->_errormessage);
            }
            $result = true;
        }

        return $result;
    }

    /**
     * Send HTTP 401 authentication headers.
     */
    protected function sendAuthHeaders(): void
    {
        header('WWW-Authenticate: Basic realm="' . $this->_title . '"');
        header('HTTP/1.0 401 Unauthorized');
    }

    /**
     * Check if user is authenticated (without prompting).
     */
    public function isAuthenticated(): bool
    {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            return false;
        }

        $providedUsername = $_SERVER['PHP_AUTH_USER'];
        $providedPassword = $_SERVER['PHP_AUTH_PW'] ?? '';

        if ($this->_onauthenticate !== null) {
            return (bool)$this->callEvent('onauthenticate', [
                'username' => $providedUsername,
                'password' => $providedPassword,
            ]);
        }

        return $providedUsername === $this->_username && $providedPassword === $this->_password;
    }

    /**
     * Get the currently authenticated username.
     */
    public function getAuthenticatedUsername(): ?string
    {
        return $_SERVER['PHP_AUTH_USER'] ?? null;
    }

    // Legacy getters/setters
    public function getTitle(): string { return $this->_title; }
    public function setTitle(string $value): void { $this->Title = $value; }
    public function defaultTitle(): string { return 'Login'; }

    public function getErrorMessage(): string { return $this->_errormessage; }
    public function setErrorMessage(string $value): void { $this->ErrorMessage = $value; }
    public function defaultErrorMessage(): string { return 'Unauthorized'; }

    public function getUsername(): string { return $this->_username; }
    public function setUsername(string $value): void { $this->Username = $value; }
    public function defaultUsername(): string { return ''; }

    public function getPassword(): string { return $this->_password; }
    public function setPassword(string $value): void { $this->Password = $value; }
    public function defaultPassword(): string { return ''; }

    public function getOnAuthenticate(): ?string { return $this->_onauthenticate; }
    public function setOnAuthenticate(?string $value): void { $this->OnAuthenticate = $value; }
    public function defaultOnAuthenticate(): ?string { return null; }
}
