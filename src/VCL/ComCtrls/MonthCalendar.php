<?php

declare(strict_types=1);

namespace VCL\ComCtrls;

use VCL\UI\FocusControl;

/**
 * MonthCalendar displays a calendar for date selection.
 *
 * Use MonthCalendar to allow users to select a date from a visual calendar.
 * This is a simplified HTML5 version that uses the native date input type.
 *
 * PHP 8.4 version with Property Hooks.
 */
class MonthCalendar extends FocusControl
{
    protected string $_timezone = 'UTC';
    protected bool $_showstime = true;
    protected int $_firstday = 1;
    protected string $_date = '';
    protected string $_dateformat = '%m-%d-%Y %I:%M';
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

    public int $FirstDay {
        get => $this->_firstday;
        set => $this->_firstday = max(0, min(6, $value));
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
        $this->_width = 200;
        $this->_height = 200;
    }

    /**
     * Pre-initialization: read submitted date.
     */
    public function preinit(): void
    {
        parent::preinit();

        $fieldName = $this->Name . '_date';
        $submitted = $this->input->$fieldName ?? null;

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

        if (!defined($this->Name . '_update')) {
            define($this->Name . '_update', 1);

            $name = htmlspecialchars($this->Name);

            echo <<<JS
function {$name}_update(event) {
    var input = document.getElementById('{$name}_input');
    var hidden = document.getElementById('{$name}_date');
    hidden.value = input.value;
JS;

            if (($this->ControlState & CS_DESIGNING) !== CS_DESIGNING && $this->_jsonupdate !== null) {
                $handler = htmlspecialchars($this->_jsonupdate);
                echo "\n    {$handler}(event);";
            }

            echo "\n}\n";
        }
    }

    /**
     * Render the month calendar.
     */
    protected function dumpContents(): void
    {
        $name = htmlspecialchars($this->Name);
        $class = $this->readStyleClass();
        $classAttr = $class !== '' ? " class=\"{$class}\"" : '';

        // Hidden field for form submission
        echo "<input type=\"hidden\" name=\"{$name}_date\" id=\"{$name}_date\" value=\"{$this->_date}\" />\n";

        // Determine input type based on ShowsTime
        $inputType = $this->_showstime ? 'datetime-local' : 'date';

        // Convert stored date to HTML5 format
        $htmlDate = $this->_date;
        if ($htmlDate === '') {
            $htmlDate = $this->_showstime ? date('Y-m-d\TH:i') : date('Y-m-d');
        }

        $style = "width:{$this->_width}px;height:{$this->_height}px;";

        if ($this->Hidden && ($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
            $style .= "visibility:hidden;";
        }

        echo "<div id=\"{$name}_container\" style=\"{$style}\"{$classAttr}>\n";
        echo "<input type=\"{$inputType}\" id=\"{$name}_input\" value=\"{$htmlDate}\"";
        echo " style=\"width:100%;height:100%;\"";
        echo " onchange=\"{$name}_update(event)\"";

        if (!$this->_enabled) {
            echo " disabled";
        }

        echo " />\n";
        echo "</div>\n";
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

    public function getFirstDay(): int { return $this->_firstday; }
    public function setFirstDay(int $value): void { $this->FirstDay = $value; }
    public function defaultFirstDay(): int { return 1; }

    public function getDate(): string { return $this->_date; }
    public function setDate(string $value): void { $this->Date = $value; }
    public function defaultDate(): string { return ''; }

    public function getDateFormat(): string { return $this->_dateformat; }
    public function setDateFormat(string $value): void { $this->DateFormat = $value; }
    public function defaultDateFormat(): string { return '%m-%d-%Y %I:%M'; }

    public function getjsOnUpdate(): ?string { return $this->_jsonupdate; }
    public function setjsOnUpdate(?string $value): void { $this->jsOnUpdate = $value; }
    public function defaultjsOnUpdate(): ?string { return null; }
}
