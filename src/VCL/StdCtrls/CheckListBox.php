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

namespace VCL\StdCtrls;

/**
 * CheckListBox displays a list with check boxes next to each item.
 *
 * CheckListBox is similar to ListBox, except that each item has a check box next to it.
 * Users can check or uncheck items in the list.
 *
 * Example usage:
 * ```php
 * $checkList = new CheckListBox($this);
 * $checkList->Name = 'CheckListBox1';
 * $checkList->Parent = $this;
 * $checkList->Items = ['Item 1', 'Item 2', 'Item 3'];
 * $checkList->OnClick = 'CheckListBox1Click';
 * ```
 */
class CheckListBox extends CustomCheckListBox
{
    // All properties are inherited from CustomCheckListBox
    // This class exists for API consistency with Delphi VCL
}
