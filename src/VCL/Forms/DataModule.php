<?php

declare(strict_types=1);

namespace VCL\Forms;

/**
 * DataModule is a non-visible container for components.
 *
 * Used to create non-visible pages that hold database connections,
 * queries, and other non-visual components for reuse across pages.
 *
 * PHP 8.4 version.
 */
class DataModule extends CustomPage
{
    /**
     * Override show to do nothing since DataModule is non-visual.
     */
    public function show(): void
    {
        // DataModule is non-visual, don't render anything
    }
}
