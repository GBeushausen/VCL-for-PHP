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

namespace VCL\ExtCtrls;

/**
 * A live clock using modern JavaScript.
 *
 * Displays a real-time clock that updates every second.
 * Supports alarms and custom formatting.
 *
 * Example usage:
 * ```php
 * $clock = new Clock($this);
 * $clock->Name = 'Clock1';
 * $clock->Parent = $this;
 * $clock->Left = 20;
 * $clock->Top = 20;
 * $clock->ShowSeconds = true;
 * $clock->Format24h = true;
 * $clock->jsOnAlarm = 'function() { alert("Alarm!"); }';
 * $clock->AlarmTime = '+5000'; // 5 seconds from now
 * ```
 */
class Clock extends Panel
{
    protected string $_alarmtime = '';
    protected ?string $_jsonalarm = null;
    protected bool $_showseconds = true;
    protected bool $_showdate = false;
    protected bool $_format24h = true;
    protected string $_dateformat = 'YYYY-MM-DD';
    protected string $_timeformat = '';
    protected int $_borderstyle = BS_SINGLE;
    protected string $_bordercolor = '#ccc';

    // =========================================================================
    // PROPERTY HOOKS
    // =========================================================================

    /**
     * Alarm time in milliseconds or as JavaScript expression.
     *
     * Can be:
     * - A timestamp in milliseconds
     * - A relative offset like '+5000' (5 seconds from now)
     * - JavaScript code like 'Date.now() + 60000'
     */
    public string $AlarmTime {
        get => $this->_alarmtime;
        set => $this->_alarmtime = $value;
    }

    /**
     * JavaScript function to call when alarm fires.
     */
    public ?string $jsOnAlarm {
        get => $this->_jsonalarm;
        set => $this->_jsonalarm = $value;
    }

    /**
     * Whether to show seconds.
     */
    public bool $ShowSeconds {
        get => $this->_showseconds;
        set => $this->_showseconds = $value;
    }

    /**
     * Whether to show the date.
     */
    public bool $ShowDate {
        get => $this->_showdate;
        set => $this->_showdate = $value;
    }

    /**
     * Use 24-hour format (true) or 12-hour with AM/PM (false).
     */
    public bool $Format24h {
        get => $this->_format24h;
        set => $this->_format24h = $value;
    }

    /**
     * Date format (YYYY-MM-DD, DD/MM/YYYY, MM/DD/YYYY, etc.).
     */
    public string $DateFormat {
        get => $this->_dateformat;
        set => $this->_dateformat = $value;
    }

    /**
     * Custom time format. Leave empty for automatic based on Format24h and ShowSeconds.
     */
    public string $TimeFormat {
        get => $this->_timeformat;
        set => $this->_timeformat = $value;
    }

    // =========================================================================
    // CONSTRUCTOR
    // =========================================================================

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->Width = 120;
        $this->Height = 40;

        // Center the text
        $this->Font->Align = 'taCenter';
    }

    // =========================================================================
    // RENDERING
    // =========================================================================

    public function dumpJavascript(): void
    {
        parent::dumpJavascript();

        // Output the Clock class only once
        if (!defined('VCL_CLOCK_JS')) {
            define('VCL_CLOCK_JS', 1);

            echo <<<'JS'
class VCLClock {
    constructor(elementId, options = {}) {
        this.element = document.getElementById(elementId);
        this.options = Object.assign({
            showSeconds: true,
            showDate: false,
            format24h: true,
            dateFormat: 'YYYY-MM-DD',
            timeFormat: '',
            alarmTime: null,
            onAlarm: null
        }, options);
        this.alarmFired = false;
        this.start();
    }

    start() {
        this.update();
        this.interval = setInterval(() => this.update(), 1000);
    }

    stop() {
        if (this.interval) {
            clearInterval(this.interval);
        }
    }

    update() {
        const now = new Date();
        let display = '';

        if (this.options.showDate) {
            display += this.formatDate(now) + ' ';
        }

        display += this.formatTime(now);
        this.element.textContent = display;

        // Check alarm
        if (this.options.alarmTime && !this.alarmFired) {
            let alarmMs = this.options.alarmTime;
            if (typeof alarmMs === 'string' && alarmMs.startsWith('+')) {
                alarmMs = Date.now() + parseInt(alarmMs.substring(1), 10);
                this.options.alarmTime = alarmMs;
            }
            if (Date.now() >= alarmMs) {
                this.alarmFired = true;
                if (typeof this.options.onAlarm === 'function') {
                    this.options.onAlarm();
                }
            }
        }
    }

    formatTime(date) {
        if (this.options.timeFormat) {
            return this.applyFormat(date, this.options.timeFormat);
        }

        let hours = date.getHours();
        let minutes = date.getMinutes();
        let seconds = date.getSeconds();
        let ampm = '';

        if (!this.options.format24h) {
            ampm = hours >= 12 ? ' PM' : ' AM';
            hours = hours % 12;
            hours = hours ? hours : 12;
        }

        let result = this.pad(hours) + ':' + this.pad(minutes);
        if (this.options.showSeconds) {
            result += ':' + this.pad(seconds);
        }
        return result + ampm;
    }

    formatDate(date) {
        const y = date.getFullYear();
        const m = this.pad(date.getMonth() + 1);
        const d = this.pad(date.getDate());

        switch (this.options.dateFormat) {
            case 'DD/MM/YYYY': return d + '/' + m + '/' + y;
            case 'MM/DD/YYYY': return m + '/' + d + '/' + y;
            case 'DD.MM.YYYY': return d + '.' + m + '.' + y;
            default: return y + '-' + m + '-' + d;
        }
    }

    pad(n) {
        return n < 10 ? '0' + n : '' + n;
    }
}
JS;
            echo "\n";
        }

        // Initialize this clock instance
        $options = json_encode([
            'showSeconds' => $this->_showseconds,
            'showDate' => $this->_showdate,
            'format24h' => $this->_format24h,
            'dateFormat' => $this->_dateformat,
            'timeFormat' => $this->_timeformat,
            'alarmTime' => $this->_alarmtime !== '' ? $this->_alarmtime : null,
        ]);

        $onAlarm = $this->_jsonalarm ?? 'null';

        echo "document.addEventListener('DOMContentLoaded', function() {\n";
        echo "  var options = {$options};\n";
        echo "  options.onAlarm = {$onAlarm};\n";
        echo "  window.{$this->Name} = new VCLClock('{$this->Name}_display', options);\n";
        echo "});\n";
    }

    public function dumpContents(): void
    {
        // Get panel styling
        $style = $this->buildStyleString();

        echo '<div id="' . htmlspecialchars($this->Name) . '" style="' . $style . '">';
        echo '<span id="' . htmlspecialchars($this->Name) . '_display" style="display:block;text-align:center;line-height:' . $this->Height . 'px;">';
        echo '--:--:--';
        echo '</span>';
        echo '</div>';
    }

    // =========================================================================
    // PROTECTED METHODS
    // =========================================================================

    protected function buildStyleString(): string
    {
        $style = 'position:absolute;';
        $style .= 'left:' . $this->Left . 'px;';
        $style .= 'top:' . $this->Top . 'px;';
        $style .= 'width:' . $this->Width . 'px;';
        $style .= 'height:' . $this->Height . 'px;';

        if ($this->Color !== '') {
            $style .= 'background-color:' . $this->Color . ';';
        }

        $style .= $this->Font->FontString;

        if ($this->_borderstyle !== BS_NONE) {
            $style .= 'border:1px solid ' . $this->_bordercolor . ';';
        }

        return $style;
    }

    // =========================================================================
    // DEFAULT VALUE METHODS
    // =========================================================================

    protected function defaultAlarmTime(): string
    {
        return '';
    }

    protected function defaultShowSeconds(): bool
    {
        return true;
    }

    protected function defaultShowDate(): bool
    {
        return false;
    }

    protected function defaultFormat24h(): bool
    {
        return true;
    }

    protected function defaultDateFormat(): string
    {
        return 'YYYY-MM-DD';
    }
}

// Border style constant
if (!defined('BS_NONE')) {
    define('BS_NONE', 0);
}
