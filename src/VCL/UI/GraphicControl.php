<?php

declare(strict_types=1);

namespace VCL\UI;

/**
 * GraphicControl is the base class for controls with graphic capabilities.
 *
 * GraphicControl provides a foundation for controls that render graphics
 * but don't have child controls or receive focus. Examples include
 * Image, Shape, PaintBox, etc.
 *
 * Note: GraphicControl extends Control directly (not FocusControl) because
 * graphic controls typically don't need focus handling or child management.
 *
 * PHP 8.4 version.
 */
class GraphicControl extends Control
{
    // Reserved for future implementation of graphic control features
    // such as canvas access, painting events, etc.
}
