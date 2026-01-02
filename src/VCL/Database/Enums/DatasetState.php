<?php

declare(strict_types=1);

namespace VCL\Database\Enums;

/**
 * DataSet state enumeration.
 */
enum DatasetState: int
{
    case Inactive = 1;
    case Browse = 2;
    case Edit = 3;
    case Insert = 4;
    case SetKey = 5;
    case CalcFields = 6;
    case Filter = 7;
    case NewValue = 8;
    case OldValue = 9;
    case CurValue = 10;
    case BlockRead = 11;
    case InternalCalc = 12;
    case Opening = 13;
}
