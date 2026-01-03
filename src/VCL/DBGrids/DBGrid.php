<?php

declare(strict_types=1);

namespace VCL\DBGrids;

use VCL\UI\CustomControl;

// Alignment constants (taCenter is already defined in Graphics/constants.php)
if (!defined('taLeftJustify')) {
    define('taLeftJustify', 'taLeftJustify');
    define('taRightJustify', 'taRightJustify');
}
// Ensure taCenter is available (defined in Graphics/constants.php)
if (!defined('taCenter')) {
    define('taCenter', 'taCenter');
}

// Sort type constants
if (!defined('stText')) {
    define('stText', 'stText');
    define('stNumeric', 'stNumeric');
    define('stDate', 'stDate');
}

/**
 * DBGrid displays and manipulates records from a dataset in a tabular grid.
 *
 * Put a DBGrid object on a form to display and edit the records from a database
 * table or query. Applications can use the data grid to insert, delete, or edit
 * data in the database, or simply to display it.
 *
 * At runtime, users can use the database paginator (DBPaginator) to move through
 * data in the grid, and to insert, delete, and edit the data.
 *
 * PHP 8.4 version with Property Hooks.
 */
class DBGrid extends CustomDBGrid
{
    protected bool $_readonly = false;
    protected int $_fixedcolumns = 0;
    protected ?string $_jsondatachanged = null;
    protected ?string $_jsonrowsaved = null;
    protected ?string $_jsonrowchanged = null;
    protected ?string $_onclick = null;
    protected ?string $_ondblclick = null;
    protected string $_headerclass = '';
    protected string $_rowclass = '';
    protected string $_alternaterowclass = '';
    protected string $_selectedrowclass = '';
    protected bool $_showheader = true;
    protected bool $_striped = true;
    protected bool $_hoverable = true;
    protected bool $_bordered = true;
    protected int $_selectedrow = -1;

    // Property Hooks
    public bool $ReadOnly {
        get => $this->_readonly;
        set => $this->_readonly = $value;
    }

    public int $FixedColumns {
        get => $this->_fixedcolumns;
        set => $this->_fixedcolumns = max(0, $value);
    }

    public ?string $jsOnDataChanged {
        get => $this->_jsondatachanged;
        set => $this->_jsondatachanged = $value;
    }

    public ?string $jsOnRowSaved {
        get => $this->_jsonrowsaved;
        set => $this->_jsonrowsaved = $value;
    }

    public ?string $jsOnRowChanged {
        get => $this->_jsonrowchanged;
        set => $this->_jsonrowchanged = $value;
    }

    public ?string $OnClick {
        get => $this->_onclick;
        set => $this->_onclick = $value;
    }

    public ?string $OnDblClick {
        get => $this->_ondblclick;
        set => $this->_ondblclick = $value;
    }

    public string $HeaderClass {
        get => $this->_headerclass;
        set => $this->_headerclass = $value;
    }

    public string $RowClass {
        get => $this->_rowclass;
        set => $this->_rowclass = $value;
    }

    public string $AlternateRowClass {
        get => $this->_alternaterowclass;
        set => $this->_alternaterowclass = $value;
    }

    public string $SelectedRowClass {
        get => $this->_selectedrowclass;
        set => $this->_selectedrowclass = $value;
    }

    public bool $ShowHeader {
        get => $this->_showheader;
        set => $this->_showheader = $value;
    }

    public bool $Striped {
        get => $this->_striped;
        set => $this->_striped = $value;
    }

    public bool $Hoverable {
        get => $this->_hoverable;
        set => $this->_hoverable = $value;
    }

    public bool $Bordered {
        get => $this->_bordered;
        set => $this->_bordered = $value;
    }

    public int $SelectedRow {
        get => $this->_selectedrow;
        set => $this->_selectedrow = $value;
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->_width = 400;
        $this->_height = 200;
    }

    /**
     * Pre-initialization.
     */
    public function preinit(): void
    {
        parent::preinit();

        // Handle row selection from POST
        $submittedRow = $this->input->{$this->_name . '_selectedrow'} ?? null;
        if (is_object($submittedRow)) {
            $this->_selectedrow = (int)$submittedRow->asString();
        }

        // Handle action from POST (edit, delete)
        $action = $this->input->{$this->_name . '_action'} ?? null;
        if (is_object($action)) {
            $this->handleAction($action->asString());
        }
    }

    /**
     * Handle grid actions.
     */
    protected function handleAction(string $action): void
    {
        if ($this->_datasource === null || $this->_datasource->DataSet === null) {
            return;
        }

        $parts = explode(':', $action);
        $actionType = $parts[0] ?? '';
        $rowIndex = (int)($parts[1] ?? 0);

        $ds = $this->_datasource->DataSet;

        switch ($actionType) {
            case 'delete':
                $ds->First();
                for ($i = 0; $i < $rowIndex; $i++) {
                    $ds->Next();
                }
                if (!$ds->EOF()) {
                    $ds->Delete();
                }
                break;

            case 'select':
                $this->_selectedrow = $rowIndex;
                $ds->First();
                for ($i = 0; $i < $rowIndex; $i++) {
                    $ds->Next();
                }
                break;
        }
    }

    /**
     * Get column definitions from datasource if not set.
     */
    protected function getEffectiveColumns(): array
    {
        if (count($this->_columns) > 0) {
            return $this->_columns;
        }

        // Auto-generate columns from dataset fields
        if ($this->_datasource !== null && $this->_datasource->DataSet !== null) {
            $ds = $this->_datasource->DataSet;
            if ($ds->readActive()) {
                $fields = $ds->Fields;
                if (is_array($fields)) {
                    $columns = [];
                    foreach ($fields as $fieldname => $value) {
                        $columns[] = [
                            'Fieldname' => $fieldname,
                            'Caption' => $fieldname,
                            'Width' => 100,
                            'ReadOnly' => false,
                            'Alignment' => 'taLeftJustify',
                            'Color' => '',
                            'FontColor' => '',
                            'SortType' => 'stText',
                        ];
                    }
                    return $columns;
                }
            }
        }

        return [];
    }

    /**
     * Get display label for a field.
     */
    protected function getFieldDisplayLabel(string $fieldname, array $column): string
    {
        if (!empty($column['Caption'])) {
            return $column['Caption'];
        }

        if ($this->_datasource !== null && $this->_datasource->DataSet !== null) {
            $props = $this->_datasource->DataSet->readFieldProperties($fieldname);
            if ($props && isset($props['displaylabel'][0])) {
                return $props['displaylabel'][0];
            }
        }

        return $fieldname;
    }

    /**
     * Get display width for a field.
     */
    protected function getFieldDisplayWidth(string $fieldname, array $column): int
    {
        if (!empty($column['Width'])) {
            return (int)$column['Width'];
        }

        if ($this->_datasource !== null && $this->_datasource->DataSet !== null) {
            $props = $this->_datasource->DataSet->readFieldProperties($fieldname);
            if ($props && isset($props['displaywidth'][0])) {
                return (int)$props['displaywidth'][0];
            }
        }

        return 100;
    }

    /**
     * Build table style string.
     */
    protected function buildTableStyle(): string
    {
        $styles = [];

        if ($this->_width > 0) {
            $styles[] = "width: {$this->_width}px";
        }
        if ($this->_height > 0) {
            $styles[] = "max-height: {$this->_height}px";
            $styles[] = "overflow-y: auto";
        }

        return implode('; ', $styles);
    }

    /**
     * Build cell style string.
     */
    protected function buildCellStyle(array $column): string
    {
        $styles = [];

        if (!empty($column['Color'])) {
            $styles[] = "background-color: {$column['Color']}";
        }
        if (!empty($column['FontColor'])) {
            $styles[] = "color: {$column['FontColor']}";
        }
        if (!empty($column['Alignment'])) {
            $alignment = match ($column['Alignment']) {
                'taRightJustify' => 'right',
                'taCenter' => 'center',
                default => 'left',
            };
            $styles[] = "text-align: {$alignment}";
        }
        if (!empty($column['Width'])) {
            $styles[] = "width: {$column['Width']}px";
        }

        return implode('; ', $styles);
    }

    /**
     * Dump the grid contents.
     */
    public function dumpContents(): void
    {
        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            $this->dumpDesignTimeContents();
            return;
        }

        $columns = $this->getEffectiveColumns();
        if (count($columns) === 0) {
            echo "<!-- DBGrid: No columns defined -->\n";
            return;
        }

        if ($this->_datasource === null || $this->_datasource->DataSet === null) {
            echo "<!-- DBGrid: No datasource -->\n";
            return;
        }

        $ds = $this->_datasource->DataSet;
        if (!$ds->readActive()) {
            echo "<!-- DBGrid: DataSet not active -->\n";
            return;
        }

        $this->dumpGridCSS();
        $this->dumpGridHTML($columns, $ds);
        $this->dumpGridJS();
    }

    /**
     * Dump design-time contents.
     */
    protected function dumpDesignTimeContents(): void
    {
        $tableStyle = $this->buildTableStyle();

        echo "<div id=\"{$this->_name}\" class=\"vcl-dbgrid\" style=\"{$tableStyle}\">\n";
        echo "<table class=\"vcl-dbgrid-table\">\n";
        echo "<thead><tr><th>Column 1</th><th>Column 2</th><th>Column 3</th></tr></thead>\n";
        echo "<tbody>\n";
        for ($i = 0; $i < 3; $i++) {
            echo "<tr><td>Data</td><td>Data</td><td>Data</td></tr>\n";
        }
        echo "</tbody>\n";
        echo "</table>\n";
        echo "</div>\n";
    }

    /**
     * Dump the grid HTML.
     */
    protected function dumpGridHTML(array $columns, object $ds): void
    {
        $wrapperStyle = $this->buildTableStyle();
        $tableClasses = ['vcl-dbgrid-table'];
        if ($this->_striped) $tableClasses[] = 'striped';
        if ($this->_hoverable) $tableClasses[] = 'hoverable';
        if ($this->_bordered) $tableClasses[] = 'bordered';

        echo "<div id=\"{$this->_name}\" class=\"vcl-dbgrid\" style=\"{$wrapperStyle}\">\n";
        echo "<table class=\"" . implode(' ', $tableClasses) . "\">\n";

        // Header
        if ($this->_showheader) {
            $headerClass = $this->_headerclass !== '' ? " class=\"{$this->_headerclass}\"" : '';
            echo "<thead><tr{$headerClass}>\n";

            foreach ($columns as $column) {
                $fieldname = $column['Fieldname'] ?? '';
                $caption = $this->getFieldDisplayLabel($fieldname, $column);
                $width = $this->getFieldDisplayWidth($fieldname, $column);
                echo "<th style=\"width: {$width}px\">" . htmlspecialchars($caption) . "</th>\n";
            }

            // Delete column
            if ($this->_deletelink !== '') {
                echo "<th style=\"width: 60px\">Actions</th>\n";
            }

            echo "</tr></thead>\n";
        }

        // Body
        echo "<tbody>\n";
        $ds->First();
        $rowIndex = 0;

        while (!$ds->EOF()) {
            $rowClasses = [];
            if ($this->_rowclass !== '') {
                $rowClasses[] = $this->_rowclass;
            }
            if ($this->_striped && $rowIndex % 2 === 1 && $this->_alternaterowclass !== '') {
                $rowClasses[] = $this->_alternaterowclass;
            }
            if ($rowIndex === $this->_selectedrow && $this->_selectedrowclass !== '') {
                $rowClasses[] = $this->_selectedrowclass;
            }
            if ($rowIndex === $this->_selectedrow) {
                $rowClasses[] = 'selected';
            }

            $rowClassAttr = count($rowClasses) > 0 ? " class=\"" . implode(' ', $rowClasses) . "\"" : '';
            $rowClickHandler = " onclick=\"{$this->_name}_selectRow({$rowIndex})\"";

            echo "<tr data-row=\"{$rowIndex}\"{$rowClassAttr}{$rowClickHandler}>\n";

            foreach ($columns as $column) {
                $fieldname = $column['Fieldname'] ?? '';
                $value = $ds->fieldget($fieldname);
                $cellStyle = $this->buildCellStyle($column);
                $styleAttr = $cellStyle !== '' ? " style=\"{$cellStyle}\"" : '';

                if ($this->_readonly || ($column['ReadOnly'] ?? false)) {
                    echo "<td{$styleAttr}>" . htmlspecialchars((string)$value) . "</td>\n";
                } else {
                    $inputName = "{$this->_name}[{$rowIndex}][{$fieldname}]";
                    echo "<td{$styleAttr}><input type=\"text\" name=\"{$inputName}\" value=\"" . htmlspecialchars((string)$value) . "\" class=\"vcl-dbgrid-input\" /></td>\n";
                }
            }

            // Delete link
            if ($this->_deletelink !== '') {
                echo "<td class=\"vcl-dbgrid-actions\">";
                echo "<a href=\"#\" onclick=\"{$this->_name}_action('delete:{$rowIndex}'); return false;\">" . htmlspecialchars($this->_deletelink) . "</a>";
                echo "</td>\n";
            }

            echo "</tr>\n";

            $ds->Next();
            $rowIndex++;
        }

        echo "</tbody>\n";
        echo "</table>\n";

        // Hidden inputs for state
        echo "<input type=\"hidden\" id=\"{$this->_name}_selectedrow\" name=\"{$this->_name}_selectedrow\" value=\"{$this->_selectedrow}\" />\n";
        echo "<input type=\"hidden\" id=\"{$this->_name}_action\" name=\"{$this->_name}_action\" value=\"\" />\n";

        echo "</div>\n";
    }

    /**
     * Dump CSS for the grid.
     */
    protected function dumpGridCSS(): void
    {
        static $cssDumped = false;
        if ($cssDumped) return;
        $cssDumped = true;

        echo "<style>\n";
        echo ".vcl-dbgrid { overflow: auto; border: 1px solid #ddd; }\n";
        echo ".vcl-dbgrid-table { width: 100%; border-collapse: collapse; font-family: sans-serif; font-size: 14px; }\n";
        echo ".vcl-dbgrid-table th { background: #f5f5f5; padding: 8px 12px; text-align: left; font-weight: 600; border-bottom: 2px solid #ddd; position: sticky; top: 0; }\n";
        echo ".vcl-dbgrid-table td { padding: 6px 12px; border-bottom: 1px solid #eee; }\n";
        echo ".vcl-dbgrid-table.bordered td, .vcl-dbgrid-table.bordered th { border: 1px solid #ddd; }\n";
        echo ".vcl-dbgrid-table.striped tbody tr:nth-child(odd) { background: #fafafa; }\n";
        echo ".vcl-dbgrid-table.hoverable tbody tr:hover { background: #e8f4ff; cursor: pointer; }\n";
        echo ".vcl-dbgrid-table tbody tr.selected { background: #cce5ff !important; }\n";
        echo ".vcl-dbgrid-input { width: 100%; padding: 4px; border: 1px solid #ccc; border-radius: 3px; box-sizing: border-box; }\n";
        echo ".vcl-dbgrid-input:focus { border-color: #007bff; outline: none; }\n";
        echo ".vcl-dbgrid-actions a { color: #dc3545; text-decoration: none; }\n";
        echo ".vcl-dbgrid-actions a:hover { text-decoration: underline; }\n";
        echo "</style>\n";
    }

    /**
     * Dump JavaScript for the grid.
     */
    protected function dumpGridJS(): void
    {
        // Escape names for use in JS
        $safeName = \VCL\Security\Escaper::id($this->_name);
        $jsNameString = \VCL\Security\Escaper::jsString($this->_name);
        $formName = $this->owner !== null ? \VCL\Security\Escaper::id($this->owner->Name) : '';

        echo "<script type=\"text/javascript\">\n";

        // Row selection
        echo "function {$safeName}_selectRow(index) {\n";
        echo "  var rows = document.querySelectorAll('#{$jsNameString} tbody tr');\n";
        echo "  rows.forEach(function(row) { row.classList.remove('selected'); });\n";
        echo "  if (rows[index]) rows[index].classList.add('selected');\n";
        echo "  document.getElementById('{$jsNameString}_selectedrow').value = index;\n";

        if ($this->_jsonrowchanged !== null) {
            $safeCallback = \VCL\Security\Escaper::id($this->_jsonrowchanged);
            echo "  if (typeof {$safeCallback} === 'function') {$safeCallback}({index: index});\n";
        }

        echo "}\n";

        // Action handler
        echo "function {$safeName}_action(action) {\n";
        echo "  document.getElementById('{$jsNameString}_action').value = action;\n";
        // Use bracket notation for consistency with form name escaping
        if ($formName !== '') {
            echo "  var form = document['{$formName}'];\n";
        } else {
            echo "  var form = document.forms[0];\n";
        }
        echo "  if (form && form.submit) form.submit();\n";
        echo "}\n";

        // Data change handler
        if ($this->_jsondatachanged !== null) {
            $safeDataChanged = \VCL\Security\Escaper::id($this->_jsondatachanged);
            echo "document.querySelectorAll('#{$jsNameString} .vcl-dbgrid-input').forEach(function(input) {\n";
            echo "  input.addEventListener('change', function(e) {\n";
            echo "    if (typeof {$safeDataChanged} === 'function') {$safeDataChanged}(e);\n";
            echo "  });\n";
            echo "});\n";
        }

        // Click handler
        if ($this->_onclick !== null) {
            $safeOnClick = \VCL\Security\Escaper::id($this->_onclick);
            echo "document.getElementById('{$jsNameString}').addEventListener('click', function(e) {\n";
            echo "  if (typeof {$safeOnClick} === 'function') {$safeOnClick}(e);\n";
            echo "});\n";
        }

        // DblClick handler
        if ($this->_ondblclick !== null) {
            $safeOnDblClick = \VCL\Security\Escaper::id($this->_ondblclick);
            echo "document.getElementById('{$jsNameString}').addEventListener('dblclick', function(e) {\n";
            echo "  if (typeof {$safeOnDblClick} === 'function') {$safeOnDblClick}(e);\n";
            echo "});\n";
        }

        echo "</script>\n";
    }

    /**
     * Get the currently selected row data.
     */
    public function getSelectedRowData(): ?array
    {
        if ($this->_selectedrow < 0) {
            return null;
        }

        if ($this->_datasource === null || $this->_datasource->DataSet === null) {
            return null;
        }

        $ds = $this->_datasource->DataSet;
        $ds->First();

        for ($i = 0; $i < $this->_selectedrow; $i++) {
            $ds->Next();
            if ($ds->EOF()) {
                return null;
            }
        }

        return $ds->Fields;
    }

    // Legacy getters/setters
    public function getReadOnly(): bool { return $this->_readonly; }
    public function setReadOnly(bool $value): void { $this->ReadOnly = $value; }
    public function defaultReadOnly(): bool { return false; }

    public function getFixedColumns(): int { return $this->_fixedcolumns; }
    public function setFixedColumns(int $value): void { $this->FixedColumns = $value; }
    public function defaultFixedColumns(): int { return 0; }

    public function getjsOnDataChanged(): ?string { return $this->_jsondatachanged; }
    public function setjsOnDataChanged(?string $value): void { $this->jsOnDataChanged = $value; }

    public function getjsOnRowSaved(): ?string { return $this->_jsonrowsaved; }
    public function setjsOnRowSaved(?string $value): void { $this->jsOnRowSaved = $value; }

    public function getjsOnRowChanged(): ?string { return $this->_jsonrowchanged; }
    public function setjsOnRowChanged(?string $value): void { $this->jsOnRowChanged = $value; }

    public function getjsOnClick(): ?string { return $this->_onclick; }
    public function setjsOnClick(?string $value): void { $this->OnClick = $value; }

    public function getjsOnDblClick(): ?string { return $this->_ondblclick; }
    public function setjsOnDblClick(?string $value): void { $this->OnDblClick = $value; }

    public function getShowHeader(): bool { return $this->_showheader; }
    public function setShowHeader(bool $value): void { $this->ShowHeader = $value; }

    public function getStriped(): bool { return $this->_striped; }
    public function setStriped(bool $value): void { $this->Striped = $value; }

    public function getHoverable(): bool { return $this->_hoverable; }
    public function setHoverable(bool $value): void { $this->Hoverable = $value; }

    public function getBordered(): bool { return $this->_bordered; }
    public function setBordered(bool $value): void { $this->Bordered = $value; }
}
