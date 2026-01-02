<?php

declare(strict_types=1);

namespace VCL\ExtCtrls;

use VCL\Core\Component;

/**
 * Timer is a non-visual component that fires events at specified intervals.
 *
 * Use Timer to generate events at regular intervals. The Interval property
 * determines the time in milliseconds between events. The jsOnTimer event
 * fires when each interval passes.
 *
 * PHP 8.4 version with Property Hooks.
 */
class Timer extends Component
{
    protected int $_interval = 1000;
    protected bool $_enabled = true;
    protected ?string $_jsontimer = null;

    // Property Hooks
    public int $Interval {
        get => $this->_interval;
        set => $this->_interval = max(0, $value);
    }

    public bool $Enabled {
        get => $this->_enabled;
        set => $this->_enabled = $value;
    }

    public ?string $jsOnTimer {
        get => $this->_jsontimer;
        set => $this->_jsontimer = $value;
    }

    /**
     * Dump JavaScript timer code.
     */
    public function dumpJavascript(): void
    {
        parent::dumpJavascript();

        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            return;
        }

        if (!$this->_enabled || $this->_jsontimer === null) {
            return;
        }

        $name = htmlspecialchars($this->Name);
        $interval = $this->_interval;
        $handler = htmlspecialchars($this->_jsontimer);

        $this->dumpJSEvent($this->_jsontimer);

        echo <<<JS
  var {$name}_TimerID = null;
  var {$name}_OnLoad = null;

  function {$name}_addEvent(obj, evType, fn) {
    if (obj.addEventListener) {
      obj.addEventListener(evType, fn, false);
      return true;
    } else if (obj.attachEvent) {
      var r = obj.attachEvent("on" + evType, fn);
      return r;
    } else {
      return false;
    }
  }

  function {$name}_InitTimer() {
    if ({$name}_OnLoad != null) {$name}_OnLoad();
    {$name}_DisableTimer();
    {$name}_EnableTimer();
  }

  function {$name}_DisableTimer() {
    if ({$name}_TimerID) {
      clearTimeout({$name}_TimerID);
      {$name}_TimerID = null;
    }
  }

  function {$name}_Event() {
    var event = {}; // synthetic event for timer
    if ({$name}_TimerID) {
      {$name}_DisableTimer();
      {$handler}(event);
      {$name}_EnableTimer();
    }
  }

  function {$name}_EnableTimer() {
    {$name}_TimerID = self.setTimeout("{$name}_Event()", {$interval});
  }

  if (window.onload) {$name}_OnLoad = window.onload;
  {$name}_addEvent(window, 'load', {$name}_InitTimer);

JS;
    }

    // Legacy getters/setters
    public function getEnabled(): bool { return $this->_enabled; }
    public function setEnabled(bool $value): void { $this->Enabled = $value; }
    public function defaultEnabled(): bool { return true; }

    public function getInterval(): int { return $this->_interval; }
    public function setInterval(int $value): void { $this->Interval = $value; }
    public function defaultInterval(): int { return 1000; }

    public function getjsOnTimer(): ?string { return $this->_jsontimer; }
    public function setjsOnTimer(?string $value): void { $this->jsOnTimer = $value; }
    public function defaultjsOnTimer(): ?string { return null; }
}
