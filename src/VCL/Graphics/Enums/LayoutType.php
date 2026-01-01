<?php

declare(strict_types=1);

namespace VCL\Graphics\Enums;

/**
 * Layout type options for control rendering.
 */
enum LayoutType: string
{
    case Flow = 'FLOW_LAYOUT';
    case XY = 'XY_LAYOUT';
    case AbsXY = 'ABS_XY_LAYOUT';
    case RelXY = 'REL_XY_LAYOUT';
    case GridBag = 'GRIDBAG_LAYOUT';
    case Row = 'ROW_LAYOUT';
    case Col = 'COL_LAYOUT';
}
