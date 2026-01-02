<?php

declare(strict_types=1);

namespace VCL\Forms;

use VCL\Core\Component;

/**
 * Application class holds references to all forms in your application.
 *
 * The Application class is the root owner for all forms and pages. It handles
 * session management and application-wide language settings.
 *
 * PHP 8.4 version with Property Hooks.
 */
class Application extends Component
{
    protected static ?Application $_instance = null;
    protected string $_language = '';

    /**
     * Get the singleton instance of Application.
     */
    public static function getInstance(): Application
    {
        if (self::$_instance === null) {
            self::$_instance = new self(null);
        }
        return self::$_instance;
    }

    // Property Hooks
    public string $Language {
        get => $this->_language;
        set => $this->_language = $value;
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        // Call startup functions
        global $startup_functions;
        if (isset($startup_functions) && is_array($startup_functions)) {
            foreach ($startup_functions as $func) {
                if (is_callable($func)) {
                    $func();
                }
            }
        }

        // Start session (only if not CLI and headers not sent)
        if (PHP_SAPI !== 'cli' && !headers_sent() && session_status() === PHP_SESSION_NONE) {
            if (!session_start()) {
                throw new \RuntimeException('Cannot start session!');
            }
        }

        // Handle session restore request (only if session is active)
        if (session_status() === PHP_SESSION_ACTIVE && isset($_GET['restore_session'])) {
            if (!isset($_POST['xajax'])) {
                $_SESSION = [];
                session_destroy();
                if (PHP_SAPI !== 'cli' && !headers_sent()) {
                    if (!session_start()) {
                        throw new \RuntimeException('Cannot restart session!');
                    }
                }
            }
        }

        // Store GET parameters in session (security: only simple keys without dots)
        if (session_status() === PHP_SESSION_ACTIVE) {
            foreach ($_GET as $k => $v) {
                if (strpos($k, '.') === false && is_scalar($v)) {
                    $_SESSION[$k] = $v;
                }
            }
        }
    }

    /**
     * Auto-detect user's browser language.
     */
    public function autoDetectLanguage(): void
    {
        $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        if ($acceptLanguage === '') {
            return;
        }

        // Parse Accept-Language header
        $languages = [];
        foreach (explode(',', $acceptLanguage) as $lang) {
            $parts = explode(';', $lang);
            $code = trim($parts[0]);
            $quality = isset($parts[1]) ? (float)str_replace('q=', '', $parts[1]) : 1.0;
            $languages[$code] = $quality;
        }

        // Sort by quality
        arsort($languages);

        // Get the highest quality language
        $topLanguage = array_key_first($languages);
        if ($topLanguage !== null) {
            $this->_language = $topLanguage;
        }
    }

    /**
     * Shutdown handler - serializes all children to session.
     */
    public function shutdown(): void
    {
        $this->serializeChildren();
    }

    // Legacy getters/setters
    public function getLanguage(): string { return $this->_language; }
    public function setLanguage(string $value): void { $this->Language = $value; }
}
