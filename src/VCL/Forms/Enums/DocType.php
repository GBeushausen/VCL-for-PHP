<?php

declare(strict_types=1);

namespace VCL\Forms\Enums;

/**
 * DocType enum for HTML document type declarations.
 */
enum DocType: string
{
    case None = 'dtNone';
    case XHTML_1_0_Strict = 'dtXHTML_1_0_Strict';
    case XHTML_1_0_Transitional = 'dtXHTML_1_0_Transitional';
    case XHTML_1_0_Frameset = 'dtXHTML_1_0_Frameset';
    case HTML_4_01_Strict = 'dtHTML_4_01_Strict';
    case HTML_4_01_Transitional = 'dtHTML_4_01_Transitional';
    case HTML_4_01_Frameset = 'dtHTML_4_01_Frameset';
    case XHTML_1_1 = 'dtXHTML_1_1';
    case HTML5 = 'dtHTML5';

    /**
     * Get the DOCTYPE declaration string.
     */
    public function toDeclaration(): string
    {
        return match($this) {
            self::None => '',
            self::XHTML_1_0_Strict => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
            self::XHTML_1_0_Transitional => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
            self::XHTML_1_0_Frameset => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
            self::HTML_4_01_Strict => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
            self::HTML_4_01_Transitional => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
            self::HTML_4_01_Frameset => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
            self::XHTML_1_1 => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
            self::HTML5 => '<!DOCTYPE html>',
        };
    }

    /**
     * Get extra HTML tag attributes.
     */
    public function toHtmlAttributes(): string
    {
        return match($this) {
            self::None, self::HTML_4_01_Strict, self::HTML_4_01_Transitional, self::HTML_4_01_Frameset => '',
            self::XHTML_1_0_Strict, self::XHTML_1_0_Transitional, self::XHTML_1_0_Frameset, self::XHTML_1_1 => 'xmlns="http://www.w3.org/1999/xhtml"',
            self::HTML5 => '',
        };
    }
}
