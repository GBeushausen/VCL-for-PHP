<?php

declare(strict_types=1);

namespace VCL\DBCtrls;

use VCL\UI\CustomControl;

/**
 * DBPaginator is a control to browse through the records of a datasource.
 *
 * This control provides a set of links so the user can move through the dataset
 * attached to the datasource specified.
 *
 * PHP 8.4 version with Property Hooks.
 */
class DBPaginator extends CustomControl
{
    protected mixed $_datasource = null;
    protected ?string $_onclick = null;

    protected string $_captionfirst = 'First';
    protected string $_captionprevious = 'Prev';
    protected string $_captionlast = 'Last';
    protected string $_captionnext = 'Next';
    protected string $_orientation = 'noHorizontal';
    protected string $_pagenumberformat = '%d';
    protected bool $_showfirst = true;
    protected bool $_showlast = true;
    protected bool $_shownext = true;
    protected bool $_showprevious = true;
    protected int $_shownrecordscount = 10;
    protected int $_currentpos = -1;

    // Property Hooks
    public mixed $DataSource {
        get => $this->_datasource;
        set => $this->_datasource = $this->fixupProperty($value);
    }

    public ?string $OnClick {
        get => $this->_onclick;
        set => $this->_onclick = $value;
    }

    public string $CaptionFirst {
        get => $this->_captionfirst;
        set => $this->_captionfirst = $value;
    }

    public string $CaptionPrevious {
        get => $this->_captionprevious;
        set => $this->_captionprevious = $value;
    }

    public string $CaptionLast {
        get => $this->_captionlast;
        set => $this->_captionlast = $value;
    }

    public string $CaptionNext {
        get => $this->_captionnext;
        set => $this->_captionnext = $value;
    }

    public string $Orientation {
        get => $this->_orientation;
        set => $this->_orientation = $value;
    }

    public string $PageNumberFormat {
        get => $this->_pagenumberformat;
        set => $this->_pagenumberformat = $value;
    }

    public bool $ShowFirst {
        get => $this->_showfirst;
        set => $this->_showfirst = $value;
    }

    public bool $ShowLast {
        get => $this->_showlast;
        set => $this->_showlast = $value;
    }

    public bool $ShowNext {
        get => $this->_shownext;
        set => $this->_shownext = $value;
    }

    public bool $ShowPrevious {
        get => $this->_showprevious;
        set => $this->_showprevious = $value;
    }

    public int $ShownRecordsCount {
        get => $this->_shownrecordscount;
        set => $this->_shownrecordscount = max(1, $value);
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->_width = 300;
        $this->_height = 30;
    }

    /**
     * Serialize state.
     */
    public function serialize(): void
    {
        parent::serialize();

        $owner = $this->readOwner();
        if ($owner !== null) {
            $prefix = $owner->readNamePath() . '.' . $this->_name . '.';
            $_SESSION[$prefix . 'CurrentPos'] = $this->_currentpos;
        }
    }

    /**
     * Unserialize state.
     */
    public function unserialize(): void
    {
        parent::unserialize();

        $owner = $this->readOwner();
        if ($owner !== null) {
            $prefix = $owner->readNamePath() . '.' . $this->_name . '.';
            $this->_currentpos = $_SESSION[$prefix . 'CurrentPos'] ?? -1;
        }
    }

    /**
     * Called when component is loaded.
     */
    public function loaded(): void
    {
        parent::loaded();
        $this->DataSource = $this->_datasource;
    }

    /**
     * Pre-initialization.
     */
    public function preinit(): void
    {
        parent::preinit();

        // Restore position
        if ($this->_currentpos > 0) {
            $this->gotoPos($this->_currentpos);
        }

        // Handle submitted value
        $submittedValue = $this->input->{$this->_name} ?? null;

        if (is_object($submittedValue) && $this->_datasource !== null && $this->_datasource->DataSet !== null) {
            $value = $submittedValue->asString();
            $this->linkClicked($value);
        }
    }

    /**
     * Go to a specific position.
     */
    protected function gotoPos(int $pos): void
    {
        if ($pos >= 0 && $this->_datasource !== null && $this->_datasource->DataSet !== null) {
            $ds = $this->_datasource->DataSet;
            $ds->First();
            for ($x = 0; $x < $pos; $x++) {
                $ds->Next();
            }
        }
    }

    /**
     * Execute the action of a clicked link.
     */
    protected function linkClicked(string $action): void
    {
        if ($this->_datasource === null || $this->_datasource->DataSet === null) {
            return;
        }

        $ds = $this->_datasource->DataSet;
        $val = '';

        switch ($action) {
            case 'first':
                $ds->First();
                $val = $action;
                $this->_currentpos = 0;
                break;

            case 'prev':
                $val = $action;
                $this->_currentpos--;
                $this->gotoPos($this->_currentpos);
                break;

            case 'next':
                $ds->Next();
                $val = $action;
                if ($this->_currentpos < $ds->readRecordCount() - 1) {
                    $this->_currentpos++;
                }
                break;

            case 'last':
                $ds->Last();
                $val = $action;
                $this->_currentpos = $ds->readRecordCount() - 1;
                break;

            default:
                if (is_numeric($action)) {
                    $val = (int)$action - 1;
                    $diff = $val - $this->_currentpos;

                    if ($diff < 0) {
                        $this->gotoPos($val);
                    } else {
                        for ($x = 0; $x < abs($diff); $x++) {
                            $ds->Next();
                        }
                    }
                    $this->_currentpos = $val;
                }
                break;
        }

        $this->callEvent('onclick', ['action' => $val]);
    }

    /**
     * Simulate a link click.
     */
    public function linkClick(string $action): void
    {
        $this->linkClicked($action);
    }

    /**
     * Get array of record numbers to display.
     */
    protected function getArrayOfRecordNumbers(): array
    {
        $result = [];

        if (($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
            if ($this->_datasource !== null && $this->_datasource->DataSet !== null && $this->_datasource->DataSet->readActive()) {
                $ds = $this->_datasource->DataSet;
                $currentRecord = $this->_currentpos;
                $recordCount = $ds->readRecordCount();

                $centeroffset = (int)round($this->_shownrecordscount / 2);

                if ($currentRecord < $centeroffset) {
                    $end = min($recordCount, $this->_shownrecordscount) - 1;
                } else {
                    $end = min($recordCount - 1, $currentRecord + (int)floor($this->_shownrecordscount / 2));
                }

                $start = max(0, $currentRecord - max($centeroffset - 1, $this->_shownrecordscount - ($end - $currentRecord) - 1));

                for ($x = $start; $x <= $end; $x++) {
                    $result[$x] = [
                        'recordnumber' => $x + 1,
                        'currentrecord' => ($x === $currentRecord),
                        'first' => ($x === 0),
                        'last' => ($x === $recordCount - 1),
                    ];
                }
            }
        } else {
            for ($x = 1; $x <= $this->_shownrecordscount; $x++) {
                $result[$x - 1] = [
                    'recordnumber' => $x,
                    'currentrecord' => ($x === 1),
                    'first' => ($x === 1),
                    'last' => false,
                ];
            }
        }

        return $result;
    }

    /**
     * Dump the paginator contents.
     */
    public function dumpContents(): void
    {
        $style = $this->buildPaginatorStyle();
        $isVertical = ($this->_orientation === 'noVertical');

        echo "<nav id=\"{$this->_name}\" class=\"vcl-paginator\" style=\"{$style}\">\n";

        $numbers = $this->getArrayOfRecordNumbers();
        $currentRecord = $this->_currentpos;
        $firstShown = isset($numbers[$currentRecord]) && $numbers[$currentRecord]['first'];
        $lastShown = isset($numbers[$currentRecord]) && $numbers[$currentRecord]['last'];

        if ($isVertical) {
            echo "<ul class=\"vcl-paginator-list vertical\">\n";
        } else {
            echo "<ul class=\"vcl-paginator-list horizontal\">\n";
        }

        // First button
        if ($this->_showfirst) {
            $disabled = $firstShown ? ' disabled' : '';
            echo "<li><a href=\"#\" data-action=\"first\" class=\"vcl-page-link{$disabled}\" onclick=\"{$this->_name}_navigate('first'); return false;\">" . htmlspecialchars($this->_captionfirst) . "</a></li>\n";
        }

        // Previous button
        if ($this->_showprevious) {
            $disabled = $firstShown ? ' disabled' : '';
            echo "<li><a href=\"#\" data-action=\"prev\" class=\"vcl-page-link{$disabled}\" onclick=\"{$this->_name}_navigate('prev'); return false;\">" . htmlspecialchars($this->_captionprevious) . "</a></li>\n";
        }

        // Page numbers
        foreach ($numbers as $number) {
            $active = $number['currentrecord'] ? ' active' : '';
            $recNum = $number['recordnumber'];
            $caption = sprintf($this->_pagenumberformat, $recNum);
            echo "<li><a href=\"#\" data-action=\"{$recNum}\" class=\"vcl-page-link{$active}\" onclick=\"{$this->_name}_navigate('{$recNum}'); return false;\">{$caption}</a></li>\n";
        }

        // Next button
        if ($this->_shownext) {
            $disabled = $lastShown ? ' disabled' : '';
            echo "<li><a href=\"#\" data-action=\"next\" class=\"vcl-page-link{$disabled}\" onclick=\"{$this->_name}_navigate('next'); return false;\">" . htmlspecialchars($this->_captionnext) . "</a></li>\n";
        }

        // Last button
        if ($this->_showlast) {
            $disabled = $lastShown ? ' disabled' : '';
            echo "<li><a href=\"#\" data-action=\"last\" class=\"vcl-page-link{$disabled}\" onclick=\"{$this->_name}_navigate('last'); return false;\">" . htmlspecialchars($this->_captionlast) . "</a></li>\n";
        }

        echo "</ul>\n";
        echo "</nav>\n";

        // Hidden input for form submission
        echo "<input type=\"hidden\" id=\"{$this->_name}\" name=\"{$this->_name}\" value=\"\" />\n";

        $this->dumpPaginatorCSS();
        $this->dumpPaginatorJS();
    }

    /**
     * Build the paginator style string.
     */
    protected function buildPaginatorStyle(): string
    {
        $styles = [];

        if ($this->_width > 0) {
            $styles[] = "width: {$this->_width}px";
        }
        if ($this->_height > 0) {
            $styles[] = "height: {$this->_height}px";
        }
        if ($this->_color !== '') {
            $styles[] = "background-color: {$this->_color}";
        }
        if (!$this->_visible) {
            $styles[] = "display: none";
        }

        return implode('; ', $styles);
    }

    /**
     * Dump CSS for paginator.
     */
    protected function dumpPaginatorCSS(): void
    {
        static $cssDumped = false;
        if ($cssDumped) return;
        $cssDumped = true;

        echo "<style>\n";
        echo ".vcl-paginator { font-family: sans-serif; font-size: 14px; }\n";
        echo ".vcl-paginator-list { list-style: none; margin: 0; padding: 0; display: flex; gap: 4px; }\n";
        echo ".vcl-paginator-list.vertical { flex-direction: column; }\n";
        echo ".vcl-page-link { display: inline-block; padding: 4px 8px; text-decoration: none; color: #333; border: 1px solid #ccc; border-radius: 3px; }\n";
        echo ".vcl-page-link:hover { background: #e8e8e8; }\n";
        echo ".vcl-page-link.active { background: #007bff; color: white; border-color: #007bff; }\n";
        echo ".vcl-page-link.disabled { color: #999; pointer-events: none; }\n";
        echo "</style>\n";
    }

    /**
     * Dump JavaScript for paginator.
     */
    protected function dumpPaginatorJS(): void
    {
        $formName = $this->_owner !== null ? $this->_owner->Name : 'document.forms[0]';

        echo "<script type=\"text/javascript\">\n";
        echo "function {$this->_name}_navigate(action) {\n";
        echo "  var input = document.getElementById('{$this->_name}');\n";
        echo "  if (input) input.value = action;\n";
        echo "  var form = document.{$formName};\n";
        echo "  if (form && form.submit) form.submit();\n";
        echo "}\n";
        echo "</script>\n";
    }

    // Legacy getters/setters
    public function getDataSource(): mixed { return $this->_datasource; }
    public function setDataSource(mixed $value): void { $this->DataSource = $value; }

    public function getCaptionFirst(): string { return $this->_captionfirst; }
    public function setCaptionFirst(string $value): void { $this->CaptionFirst = $value; }

    public function getCaptionPrevious(): string { return $this->_captionprevious; }
    public function setCaptionPrevious(string $value): void { $this->CaptionPrevious = $value; }

    public function getCaptionNext(): string { return $this->_captionnext; }
    public function setCaptionNext(string $value): void { $this->CaptionNext = $value; }

    public function getCaptionLast(): string { return $this->_captionlast; }
    public function setCaptionLast(string $value): void { $this->CaptionLast = $value; }

    public function getOrientation(): string { return $this->_orientation; }
    public function setOrientation(string $value): void { $this->Orientation = $value; }

    public function getOnClick(): ?string { return $this->_onclick; }
    public function setOnClick(?string $value): void { $this->OnClick = $value; }
}
