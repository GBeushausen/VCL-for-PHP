<?php

declare(strict_types=1);

namespace VCL\ComCtrls;

use VCL\UI\FocusControl;

/**
 * DateTimePicker displays a combobox for entering dates or times.
 *
 * DateTimePicker is a visual component designed specifically for entering
 * dates or times. It features a popup calendar/time selector.
 *
 * PHP 8.4 version with Property Hooks and HTML5 date/time inputs.
 */
class DateTimePicker extends FocusControl
{
    protected string $_timezone = 'UTC';
    protected bool $_showstime = true;
    protected string $_date = '';
    protected string $_dateformat = 'Y-m-d H:i';
    protected ?string $_jsonupdate = null;

    // Property Hooks
    public string $TimeZone {
        get => $this->_timezone;
        set {
            $this->_timezone = $value;
            date_default_timezone_set($value);
        }
    }

    public bool $ShowsTime {
        get => $this->_showstime;
        set => $this->_showstime = $value;
    }

    public string $Date {
        get => $this->_date;
        set => $this->_date = $value;
    }

    public string $DateFormat {
        get => $this->_dateformat;
        set => $this->_dateformat = $value;
    }

    public ?string $jsOnUpdate {
        get => $this->_jsonupdate;
        set => $this->_jsonupdate = $value;
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        date_default_timezone_set($this->_timezone);
        $this->_width = 150;
        $this->_height = 25;
    }

    /**
     * Pre-initialization: read submitted date.
     */
    public function preinit(): void
    {
        parent::preinit();

        $submitted = $this->input->{$this->Name} ?? null;

        if (is_object($submitted) && method_exists($submitted, 'asString')) {
            $this->_date = $submitted->asString();
        } elseif (is_string($submitted)) {
            $this->_date = $submitted;
        }
    }

    /**
     * Dump JavaScript events.
     */
    public function dumpJsEvents(): void
    {
        parent::dumpJsEvents();

        $this->dumpJSEvent($this->_jsonupdate);
    }

    /**
     * Render the date time picker.
     */
    protected function dumpContents(): void
    {
        $name = htmlspecialchars($this->Name);
        $class = $this->readStyleClass();
        $classAttr = $class !== '' ? " class=\"{$class}\"" : '';

        // Determine input type based on ShowsTime
        $inputType = $this->_showstime ? 'datetime-local' : 'date';

        // Convert stored date to HTML5 format
        $htmlDate = $this->_date;
        if ($htmlDate === '' || $htmlDate === 'now') {
            $htmlDate = $this->_showstime ? date('Y-m-d\TH:i') : date('Y-m-d');
        }

        $style = "width:{$this->_width}px;";

        if ($this->Hidden && ($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
            $style .= "visibility:hidden;";
        }

        echo "<input type=\"{$inputType}\" id=\"{$name}\" name=\"{$name}\" value=\"{$htmlDate}\"";
        echo " style=\"{$style}\"{$classAttr}";

        if (($this->ControlState & CS_DESIGNING) !== CS_DESIGNING && $this->_jsonupdate !== null) {
            $handler = htmlspecialchars($this->_jsonupdate);
            echo " onchange=\"{$handler}(event)\"";
        }

        if (!$this->_enabled) {
            echo " disabled";
        }

        if ($this->_hint !== '' && $this->_showHint) {
            echo " title=\"" . htmlspecialchars($this->_hint) . "\"";
        }

        echo " />";
    }

    /**
     * Override render.
     */
    public function render(): string
    {
        ob_start();
        $this->dumpContents();
        return ob_get_clean();
    }

    // Legacy getters/setters
    public function getTimeZone(): string { return $this->_timezone; }
    public function setTimeZone(string $value): void { $this->TimeZone = $value; }
    public function defaultTimeZone(): string { return 'UTC'; }

    public function getShowsTime(): bool { return $this->_showstime; }
    public function setShowsTime(bool $value): void { $this->ShowsTime = $value; }
    public function defaultShowsTime(): bool { return true; }

    public function getDate(): string { return $this->_date; }
    public function setDate(string $value): void { $this->Date = $value; }
    public function defaultDate(): string { return ''; }

    public function getDateFormat(): string { return $this->_dateformat; }
    public function setDateFormat(string $value): void { $this->DateFormat = $value; }
    public function defaultDateFormat(): string { return 'Y-m-d H:i'; }

    public function getjsOnUpdate(): ?string { return $this->_jsonupdate; }
    public function setjsOnUpdate(?string $value): void { $this->jsOnUpdate = $value; }
    public function defaultjsOnUpdate(): ?string { return null; }
}
