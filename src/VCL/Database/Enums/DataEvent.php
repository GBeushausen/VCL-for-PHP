<?php

declare(strict_types=1);

namespace VCL\Database\Enums;

/**
 * Data event enumeration.
 */
enum DataEvent: int
{
    case FieldChange = 1;
    case RecordChange = 2;
    case DataSetChange = 3;
    case DataSetScroll = 4;
    case LayoutChange = 5;
    case UpdateRecord = 6;
    case UpdateState = 7;
    case CheckBrowseMode = 8;
    case PropertyChange = 9;
    case FieldListChange = 10;
    case FocusControl = 11;
    case ParentScroll = 12;
    case ConnectChange = 13;
    case ReconcileError = 14;
    case DisabledStateChange = 15;
}
