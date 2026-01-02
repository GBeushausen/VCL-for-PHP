<?php

declare(strict_types=1);

namespace VCL\ComCtrls\Enums;

/**
 * Label position enumeration for LabeledEdit.
 */
enum LabelPosition: string
{
    case Above = 'lpAbove';
    case Below = 'lpBelow';
    // lpLeft and lpRight are commented out in original
}
