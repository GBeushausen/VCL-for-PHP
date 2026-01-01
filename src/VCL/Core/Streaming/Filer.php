<?php

declare(strict_types=1);

namespace VCL\Core\Streaming;

use VCL\Core\VCLObject;
use VCL\Core\Collection;
use VCL\Core\Component;

/**
 * A base class that reads/writes components from/to an XML stream.
 *
 * This is an internal class used by the streaming system to load all objects
 * from an XML file. It uses the XML parser to read the file, creates objects
 * and assigns property values.
 */
class Filer extends VCLObject
{
    protected ?\XMLParser $_xmlparser = null;
    protected ?Component $_root = null;
    protected ?Component $_lastread = null;
    protected ?Collection $_parents = null;
    protected array $_properties = [];
    protected ?string $_lastproperty = null;
    protected array $_rootvars = [];

    public bool $createobjects = true;

    /**
     * Root component for streaming.
     */
    public ?Component $Root {
        get => $this->_root;
        set {
            $this->_root = $value;
            if ($value !== null) {
                $this->_rootvars = get_object_vars($value);
                $this->_parents->clear();
                $this->_parents->add($value);
            }
        }
    }

    /**
     * Initialize with XML parser.
     */
    public function __construct(\XMLParser $xmlparser)
    {
        parent::__construct();

        $this->_parents = new Collection();
        $this->_properties = [];
        $this->_rootvars = [];
        $this->_lastread = null;
        $this->_lastproperty = null;

        $this->_xmlparser = $xmlparser;
        xml_set_object($this->_xmlparser, $this);
        xml_set_element_handler($this->_xmlparser, 'tagOpen', 'tagClose');
        xml_set_character_data_handler($this->_xmlparser, 'cData');
    }

    /**
     * Process opening tags.
     */
    public function tagOpen(\XMLParser $parser, string $tag, array $attributes): void
    {
        switch ($tag) {
            case 'OBJECT':
                $this->handleObjectTag($attributes);
                break;

            case 'PROPERTY':
                $this->handlePropertyTag($attributes);
                break;

            default:
                echo "Error reading resource file, tag ({$tag}) not recognized";
                break;
        }
    }

    /**
     * Handle OBJECT tag opening.
     */
    protected function handleObjectTag(array $attributes): void
    {
        $new = true;
        $class = $attributes['CLASS'] ?? '';
        $name = $attributes['NAME'] ?? '';

        // Check if this is the root component
        if (is_object($this->_root) && !is_object($this->_lastread)) {
            if ($this->_root->classNameIs($class) || $this->_root->inheritsFrom($class)) {
                $new = false;
                $this->_lastread = $this->_root;
                $this->_lastread->Name = $name;
            }
        }

        if ($new) {
            if (class_exists($class)) {
                $this->_lastread = null;

                if ($this->createobjects) {
                    $this->_lastread = new $class($this->_root);
                    // Get correct reference from components collection
                    $items = $this->_root->components->items;
                    $this->_lastread = $items[count($items) - 1];
                } else {
                    if (array_key_exists($name, $this->_rootvars)) {
                        $this->_lastread = $this->_rootvars[$name];
                    } else {
                        echo "Error reading language resource file, object ({$name}) not found";
                        return;
                    }
                }

                $this->_lastread->ControlState = CS_LOADING;
                $this->_lastread->Name = $name;

                // Set member reference in root
                if (array_key_exists($name, $this->_rootvars)) {
                    $this->_root->$name = $this->_lastread;
                }

                // Set parent for Controls
                if ($this->_lastread->inheritsFrom('Control')) {
                    $items = $this->_parents->items;
                    $this->_lastread->Parent = $items[count($items) - 1];
                }

                $this->_parents->add($this->_lastread);
            } else {
                echo "Error reading resource file, class ({$class}) doesn't exist";
            }
        }
    }

    /**
     * Handle PROPERTY tag opening.
     */
    protected function handlePropertyTag(array $attributes): void
    {
        $name = $attributes['NAME'] ?? '';

        if (!is_object($this->_lastread)) {
            echo "Error reading resource file, property ({$name}) doesn't have an object to assign to";
            return;
        }

        $this->_lastproperty = $name;
        $this->_properties[] = $name;
    }

    /**
     * Process character data.
     */
    public function cData(\XMLParser $parser, string $cdata): void
    {
        global $use_html_entity_decode;

        if (($use_html_entity_decode ?? true) && str_contains($cdata, '&')) {
            $cdata = html_entity_decode($cdata);
            $cdata = $this->decodeUnicode($cdata);
        }

        if (!is_object($this->_lastread) || $this->_lastproperty === null) {
            return;
        }

        $aroot = $this->_lastread;
        $aproperty = $this->_lastproperty;

        // Handle nested properties
        if (count($this->_properties) > 1) {
            foreach ($this->_properties as $v) {
                if ($v === $this->_lastproperty) {
                    $aproperty = $v;
                    break;
                }
                $am = 'get' . $v;
                $aroot = $aroot->$am();
            }
        }

        $isArray = false;
        $method = 'get' . $aproperty;

        if ($aroot->methodExists($method)) {
            $value = $aroot->$method();
            $isArray = is_array($value);
        }

        $method = 'set' . $aproperty;

        if ($aroot->methodExists($method)) {
            $value = $cdata;

            if ($isArray && function_exists('safeunserialize')) {
                $value = safeunserialize($value);
            }

            $aroot->$method($value);
        } else {
            // Ignore Left/Top for non-Control Components
            $isNonControl = $aroot->inheritsFrom('Component') && !$aroot->inheritsFrom('Control');
            if (!($isNonControl && in_array($aproperty, ['Left', 'Top'], true))) {
                echo "Error setting property ({$aroot->className()}::{$this->_lastproperty}), doesn't exist";
            }
        }
    }

    /**
     * Process closing tags.
     */
    public function tagClose(\XMLParser $parser, string $tag): void
    {
        switch ($tag) {
            case 'PROPERTY':
                array_pop($this->_properties);
                $this->_lastproperty = null;
                break;

            case 'OBJECT':
                $this->handleObjectClose();
                break;
        }
    }

    /**
     * Handle OBJECT tag closing.
     */
    protected function handleObjectClose(): void
    {
        $this->_parents->delete(count($this->_parents->items) - 1);
        $this->_lastread->ControlState = 0;

        if ($this->createobjects) {
            $isPageLike = $this->_lastread->inheritsFrom('Page') ||
                          $this->_lastread->inheritsFrom('DataModule') ||
                          $this->_lastread->inheritsFrom('FlexPage');

            if ($isPageLike) {
                $this->_lastread->unserialize();
                $this->_lastread->unserializeChildren();
                $this->_lastread->loadedChildren();
                $this->_lastread->loaded();
                $this->_lastread->preinit();
                $this->_lastread->init();
            }
        }

        $items = $this->_parents->items;
        if (count($items) >= 1) {
            $this->_lastread = $items[count($items) - 1];
        } else {
            $this->_lastread = null;
        }
    }

    /**
     * Decode Unicode entities.
     */
    protected function decodeUnicode(string $str): string
    {
        if (!function_exists('mb_convert_encoding')) {
            return $str;
        }

        $pattern = '/&#([0-9]+);/';
        preg_match_all($pattern, $str, $matches);

        if (empty($matches[0])) {
            return $str;
        }

        $replacements = [];
        foreach ($matches[1] as $i => $decVal) {
            $decVal = (int)$decVal;
            $utf8Str = '';

            if ($decVal >= 0x0001 && $decVal <= 0x007F) {
                $utf8Str = chr($decVal);
            } elseif ($decVal > 0x07FF) {
                $utf8Str = chr(0xE0 | (($decVal >> 12) & 0x0F))
                         . chr(0x80 | (($decVal >> 6) & 0x3F))
                         . chr(0x80 | ($decVal & 0x3F));
            } else {
                $utf8Str = chr(0xC0 | (($decVal >> 6) & 0x1F))
                         . chr(0x80 | ($decVal & 0x3F));
            }

            $replacements[$matches[0][$i]] = $utf8Str;
        }

        $newStr = strtr($str, $replacements);
        return mb_convert_encoding($newStr, mb_internal_encoding(), 'UTF-8');
    }

    // Legacy method aliases
    public function getRoot(): ?Component
    {
        return $this->_root;
    }

    public function setRoot(?Component $value): void
    {
        $this->Root = $value;
    }

    public function VCLDecodeUnicode(string $str): string
    {
        return $this->decodeUnicode($str);
    }
}

// Ensure constants are defined
if (!defined('CS_LOADING')) {
    define('CS_LOADING', 1);
    define('CS_DESIGNING', 2);
    define('csLoading', CS_LOADING);
    define('csDesigning', CS_DESIGNING);
}
