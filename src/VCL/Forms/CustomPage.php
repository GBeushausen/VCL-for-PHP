<?php

declare(strict_types=1);

namespace VCL\Forms;

/**
 * CustomPage is the base class for Page components.
 *
 * Represents the browser page and provides basic serialization functionality.
 *
 * PHP 8.4 version.
 */
class CustomPage extends ScrollingControl
{
    // Note: $reallastresourceread is inherited from Component as array

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->_controlstyle['csAcceptsControls'] = true;
    }

    /**
     * Override serialize to store component hierarchy.
     */
    public function serialize(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            parent::serialize();
            return;
        }

        $toserialize = [];

        foreach ($this->components->items as $v) {
            $parent = '';

            // Get parent name for Controls
            if (method_exists($v, 'inheritsFrom') && $v->inheritsFrom('Control')) {
                if (isset($v->parent) && is_object($v->parent)) {
                    $parent = $v->parent->Name ?? '';
                }
            }

            $name = $v->Name ?? '';
            if ($name !== '') {
                $toserialize[$name] = [$parent, $v->className()];
            }
        }

        global $application;

        if (isset($application)) {
            $appName = $application->Name ?? 'app';
            $_SESSION['comps.' . $appName . '.' . $this->className()] = $toserialize;
        }

        // Store last resource read
        $namePath = $this->readNamePath();
        $_SESSION[$namePath . '._reallastresourceread'] = $this->reallastresourceread;

        parent::serialize();
    }
}
