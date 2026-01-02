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

namespace VCL\ComCtrls;

use VCL\UI\Control;
use VCL\Database\Datasource;

/**
 * A control to paginate sets of data.
 *
 * Pager provides an interface to browse data using pages.
 * Specify the total records and records per page, and the control
 * provides buttons for navigation.
 *
 * Can be linked to a Datasource for automatic dataset pagination.
 *
 * Example usage:
 * ```php
 * $pager = new Pager($this);
 * $pager->Name = 'Pager1';
 * $pager->Parent = $this;
 * $pager->RecordsPerPage = 20;
 * $pager->DesignTotalRecords = 100;
 *
 * // Or link to a datasource:
 * $pager->Datasource = $this->Datasource1;
 * ```
 */
class Pager extends Control
{
    public int $total_records = 0;
    public int $number_of_pages = 0;
    public int $offset = 0;
    public int $current_page = 1;

    protected int $updated = 0;
    protected string $baseurl = '';

    protected int $_designtotalrecords = 100;
    protected int $_recordsperpage = 10;
    protected int $_maxbuttons = 10;
    protected string $_cssfile = 'pager.css';
    protected string $_nextcaption = 'Next &raquo;';
    protected string $_previouscaption = '&laquo; Previous';
    protected ?Datasource $_datasource = null;

    // =========================================================================
    // PROPERTY HOOKS
    // =========================================================================

    /**
     * Maximum number of records for the pager (design-time or non-datasource use).
     */
    public int $DesignTotalRecords {
        get => $this->_designtotalrecords;
        set => $this->_designtotalrecords = $value;
    }

    /**
     * Number of records shown on each page.
     */
    public int $RecordsPerPage {
        get => $this->_recordsperpage;
        set => $this->_recordsperpage = $value;
    }

    /**
     * Maximum number of page buttons to show before showing "..." navigation.
     */
    public int $MaxButtons {
        get => $this->_maxbuttons;
        set => $this->_maxbuttons = $value;
    }

    /**
     * CSS file to use for styling (loaded from VCL css folder).
     */
    public string $CSSFile {
        get => $this->_cssfile;
        set => $this->_cssfile = $value;
    }

    /**
     * Caption for the "Next" button.
     */
    public string $NextCaption {
        get => $this->_nextcaption;
        set => $this->_nextcaption = $value;
    }

    /**
     * Caption for the "Previous" button.
     */
    public string $PreviousCaption {
        get => $this->_previouscaption;
        set => $this->_previouscaption = $value;
    }

    /**
     * Datasource to link for automatic pagination.
     */
    public ?Datasource $Datasource {
        get => $this->_datasource;
        set => $this->_datasource = $this->fixupProperty($value);
    }

    /**
     * Current page number (1-based).
     */
    public int $CurrentPage {
        get => $this->current_page;
        set => $this->current_page = max(1, $value);
    }

    /**
     * Total number of pages.
     */
    public int $PageCount {
        get => $this->number_of_pages;
    }

    // =========================================================================
    // CONSTRUCTOR
    // =========================================================================

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->Width = 377;
        $this->Height = 33;
    }

    // =========================================================================
    // LIFECYCLE METHODS
    // =========================================================================

    public function loaded(): void
    {
        parent::loaded();
        $this->Datasource = $this->_datasource;
    }

    public function serialize(): void
    {
        parent::serialize();

        // Store the current page in session
        $owner = $this->readOwner();
        if ($owner !== null) {
            $prefix = $owner->readNamePath() . '.' . $this->_name . '.';
            $_SESSION[$prefix . '_current_page'] = $this->current_page;
        }
    }

    public function unserialize(): void
    {
        parent::unserialize();

        // Recover current page from session
        $owner = $this->readOwner();
        if ($owner !== null) {
            $prefix = $owner->readNamePath() . '.' . $this->_name . '.';
            $this->current_page = $_SESSION[$prefix . '_current_page'] ?? 1;
            if ($this->current_page <= 0) {
                $this->current_page = 1;
            }
        }
    }

    public function init(): void
    {
        parent::init();

        // Recover position from request
        $position = $this->input->{$this->_name} ?? null;
        if (is_object($position)) {
            $this->current_page = $position->asInteger();
        }

        $this->updateControls();
    }

    // =========================================================================
    // RENDERING
    // =========================================================================

    public function dumpHeaderCode(): void
    {
        echo '<style>' . "\n";

        // Include CSS file if specified
        if ($this->_cssfile !== '' && defined('VCL_HTTP_PATH')) {
            echo '@import "' . VCL_HTTP_PATH . '/css/' . htmlspecialchars($this->_cssfile) . '";' . "\n";
        }

        // Default styles for pagination
        echo '.vcl-pager { display: flex; gap: 4px; align-items: center; }' . "\n";
        echo '.vcl-pager a, .vcl-pager span { padding: 6px 12px; text-decoration: none; border: 1px solid #ddd; border-radius: 4px; }' . "\n";
        echo '.vcl-pager a:hover { background-color: #f5f5f5; }' . "\n";
        echo '.vcl-pager .current { background-color: #007bff; color: white; border-color: #007bff; }' . "\n";
        echo '.vcl-pager .disabled { color: #999; cursor: not-allowed; }' . "\n";

        // Apply font styles
        if ($this->_font !== null) {
            echo '.vcl-pager a, .vcl-pager span { ' . $this->_font->FontString . ' }' . "\n";
        }

        echo '</style>' . "\n";
    }

    public function dumpContents(): void
    {
        $this->updateControls();

        // Get the script filename
        $script = basename($_SERVER['PHP_SELF'] ?? '');
        $this->baseurl = $script . '?' . urlencode($this->_name) . '=%d';

        // Set height and width
        $style = 'height:' . $this->Height . 'px;width:' . $this->Width . 'px;';

        // Start output
        $output = '<div class="vcl-pager" style="' . $style . '">';

        // Previous button
        if ($this->current_page > 1) {
            $output .= '<a href="' . sprintf($this->baseurl, $this->current_page - 1) . '" class="prev">' . $this->_previouscaption . '</a>' . "\n";
        } else {
            $output .= '<span class="disabled">' . $this->_previouscaption . '</span>' . "\n";
        }

        // Window calculation for page number buttons
        $curWindowNum = (int)ceil($this->current_page / $this->_maxbuttons);
        $maxWindowNum = (int)ceil($this->number_of_pages / $this->_maxbuttons);

        // Show "..." for previous window
        if ($curWindowNum > 1) {
            $output .= '<a href="' . sprintf($this->baseurl, ($curWindowNum - 1) * $this->_maxbuttons) . '">&hellip;</a>' . "\n";
        }

        // Page number buttons
        $startPage = 1 + (($curWindowNum - 1) * $this->_maxbuttons);
        $endPage = min($curWindowNum * $this->_maxbuttons, $this->number_of_pages);

        for ($page = $startPage; $page <= $endPage; $page++) {
            if ($page === $this->current_page) {
                $output .= '<span class="current">' . $page . '</span>' . "\n";
            } else {
                $output .= '<a href="' . sprintf($this->baseurl, $page) . '">' . $page . '</a>' . "\n";
            }
        }

        // Show "..." for next window
        if ($curWindowNum < $maxWindowNum) {
            $output .= '<a href="' . sprintf($this->baseurl, $curWindowNum * $this->_maxbuttons + 1) . '">&hellip;</a>' . "\n";
        }

        // Next button
        if ($this->current_page < $this->number_of_pages && $this->number_of_pages !== 1) {
            $output .= '<a href="' . sprintf($this->baseurl, $this->current_page + 1) . '" class="next">' . $this->_nextcaption . '</a>' . "\n";
        } else {
            $output .= '<span class="disabled">' . $this->_nextcaption . '</span>' . "\n";
        }

        $output .= '</div>';

        echo $output;
    }

    // =========================================================================
    // INTERNAL METHODS
    // =========================================================================

    /**
     * Updates internal variables based on current settings.
     */
    protected function updateControls(): void
    {
        if ($this->updated) {
            return;
        }

        $this->updated = 1;
        $this->total_records = $this->_designtotalrecords;
        $ds = null;

        // If runtime and datasource is attached
        if (($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
            if ($this->_datasource !== null &&
                $this->_datasource->Dataset !== null &&
                $this->_datasource->Dataset->Active) {

                $ds = $this->_datasource->Dataset;
                $ds->LimitStart = -1;
                $ds->LimitCount = -1;
                $ds->close();
                $ds->open();
                $this->total_records = $ds->RecordCount;
            }
        }

        // Calculate total number of pages
        $this->number_of_pages = (int)ceil($this->total_records / $this->_recordsperpage);

        if ($this->current_page > $this->number_of_pages) {
            $this->current_page = max(1, $this->number_of_pages);
        }

        // Calculate the starting offset
        $this->offset = $this->_recordsperpage * ($this->current_page - 1);

        // Set dataset limits if attached
        if ($ds !== null) {
            $ds->LimitStart = $this->offset;
            $ds->LimitCount = $this->_recordsperpage;
            $ds->close();
            $ds->open();
        }
    }

    // =========================================================================
    // PUBLIC METHODS
    // =========================================================================

    /**
     * Go to a specific page.
     */
    public function goToPage(int $page): void
    {
        $this->current_page = max(1, min($page, $this->number_of_pages));
        $this->updated = 0;
        $this->updateControls();
    }

    /**
     * Go to the first page.
     */
    public function firstPage(): void
    {
        $this->goToPage(1);
    }

    /**
     * Go to the last page.
     */
    public function lastPage(): void
    {
        $this->goToPage($this->number_of_pages);
    }

    /**
     * Go to the next page.
     */
    public function nextPage(): void
    {
        $this->goToPage($this->current_page + 1);
    }

    /**
     * Go to the previous page.
     */
    public function previousPage(): void
    {
        $this->goToPage($this->current_page - 1);
    }

    /**
     * Get the current offset for database queries.
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    // =========================================================================
    // DEFAULT VALUE METHODS
    // =========================================================================

    protected function defaultDesignTotalRecords(): int
    {
        return 100;
    }

    protected function defaultRecordsPerPage(): int
    {
        return 10;
    }

    protected function defaultMaxButtons(): int
    {
        return 10;
    }

    protected function defaultCSSFile(): string
    {
        return 'pager.css';
    }

    protected function defaultNextCaption(): string
    {
        return 'Next &raquo;';
    }

    protected function defaultPreviousCaption(): string
    {
        return '&laquo; Previous';
    }
}

// Define CS_DESIGNING constant if not already defined
if (!defined('CS_DESIGNING')) {
    define('CS_DESIGNING', 1);
}
