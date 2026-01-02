<?php

declare(strict_types=1);

namespace VCL\Styles;

use VCL\Core\Component;

/**
 * Base class for StyleSheet component.
 *
 * This component allows you to link a StyleSheet file stored on a .css file.
 * Components having Style property will show available styles populated by this component.
 *
 * @link http://www.w3.org/Style/CSS/
 *
 * PHP 8.4 version with Property Hooks.
 */
class CustomStyleSheet extends Component
{
    protected string $_filename = '';
    protected array $_stylelist = [];
    protected bool $_inclstandard = false;
    protected bool $_inclid = false;
    protected bool $_incsubstyle = false;

    // Property Hooks
    public string $FileName {
        get => $this->_filename;
        set {
            $this->_filename = $value;
            $this->parseCSSFile();
        }
    }

    public bool $IncludeStandard {
        get => $this->_inclstandard;
        set => $this->_inclstandard = $value;
    }

    public bool $IncludeID {
        get => $this->_inclid;
        set => $this->_inclid = $value;
    }

    public bool $IncludeSubStyle {
        get => $this->_incsubstyle;
        set => $this->_incsubstyle = $value;
    }

    public array $Styles {
        get {
            $this->parseCSSFile();
            return $this->_stylelist;
        }
        set => $this->_stylelist = $value;
    }

    /**
     * Builds and returns an array of Style names based on specified parameters.
     *
     * @param string $fileName Name of the CSS file to parse
     * @param bool $inclStandard If true, will include standard styles
     * @param bool $inclID If true, will also include ID selectors
     * @param bool $inclSubStyle If true, will include substyles
     * @return array Array with styles available in the file
     */
    public function buildStyleList(string $fileName, bool $inclStandard, bool $inclID, bool $inclSubStyle): array
    {
        $styles = [];

        if ($fileName === '' || !file_exists($fileName)) {
            return $styles;
        }

        $file = fopen($fileName, 'r');
        if ($file === false) {
            return $styles;
        }

        // Preload file and parse out comments
        $lines = [];
        $inComment = false;

        while (!feof($file)) {
            $line = fgets($file, 4096);
            if ($line === false) {
                break;
            }
            $line = trim($line);

            while ($line !== '') {
                if ($inComment) {
                    $pos = strpos($line, '*/');
                    if ($pos === false) {
                        $line = '';
                    } else {
                        $line = substr($line, $pos + 2);
                        $inComment = false;
                    }
                } else {
                    $pos = strpos($line, '/*');
                    if ($pos === false) {
                        $lines[] = $line;
                        $line = '';
                    } else {
                        $inComment = true;
                        if ($pos !== 0) {
                            $temp = trim(substr($line, 0, $pos));
                            if ($temp !== '') {
                                $lines[] = $temp;
                            }
                        }
                        $line = substr($line, $pos + 2);
                    }
                }
            }
        }
        fclose($file);

        if (count($lines) === 0) {
            return $styles;
        }

        // Parse lines and remove CSS definitions (content between braces)
        $lines2 = [];
        $inBlock = false;

        foreach ($lines as $line) {
            while ($line !== '') {
                if ($inBlock) {
                    $pos = strpos($line, '}');
                    if ($pos === false) {
                        $line = '';
                    } else {
                        $line = trim(substr($line, $pos + 1));
                        $inBlock = false;
                    }
                } else {
                    $pos = strpos($line, '{');
                    if ($pos === false) {
                        if ($line !== '' && !in_array($line, $lines2)) {
                            $lines2[] = $line;
                        }
                        $line = '';
                    } else {
                        $inBlock = true;
                        if ($pos !== 0) {
                            $temp = trim(substr($line, 0, $pos));
                            if ($temp !== '' && !in_array($temp, $lines2)) {
                                $lines2[] = $temp;
                            }
                        }
                        $line = trim(substr($line, $pos + 1));
                    }
                }
            }
        }

        if (count($lines2) === 0) {
            return $styles;
        }

        // Prepare style list
        foreach ($lines2 as $line) {
            $words = explode(',', $line);
            foreach ($words as $word) {
                $word = trim($word);
                if ($word === '') {
                    continue;
                }

                if (!$inclSubStyle) {
                    $pos1 = strpos($word, '.');
                    $pos2 = strpos($word, '#');

                    if ($pos1 === 0 || $pos2 === 0) {
                        $prefix = $word[0];
                        $word = trim(substr($word, 1));
                        $parts = preg_split('/[ .#]/', $word);
                        $part = $prefix . trim($parts[0] ?? '');
                    } else {
                        $parts = preg_split('/[ .#]/', $word);
                        $part = trim($parts[0] ?? '');
                    }
                } else {
                    $part = $word;
                }

                if (trim($part) === '') {
                    continue;
                }

                if (in_array($part, $styles)) {
                    continue;
                }

                $pos1 = strpos($part, '.');
                $pos2 = strpos($part, '#');

                // Determine if this style should be included
                $include = false;
                if ($inclStandard && $pos1 === false && $pos2 === false) {
                    // Standard HTML tag
                    $include = true;
                } elseif ($inclID && $pos2 === 0) {
                    // ID selector
                    $include = true;
                } elseif ($pos1 === 0) {
                    // Class selector
                    $include = true;
                }

                if ($include) {
                    $styles[] = $part;
                }
            }
        }

        return $styles;
    }

    /**
     * Parses the CSS file and populates the style list.
     */
    protected function parseCSSFile(): void
    {
        $this->_stylelist = $this->buildStyleList(
            $this->_filename,
            $this->_inclstandard,
            $this->_inclid,
            $this->_incsubstyle
        );
    }

    /**
     * Dump header code (link to CSS file).
     */
    public function dumpHeaderCode(): void
    {
        if ($this->_filename !== '') {
            echo "<link rel=\"stylesheet\" href=\"{$this->_filename}\" type=\"text/css\" />\n";
        }
    }

    /**
     * Called when component is loaded.
     */
    public function loaded(): void
    {
        parent::loaded();
        $this->parseCSSFile();
    }

    /**
     * Get styles matching a pattern.
     */
    public function getStylesMatching(string $pattern): array
    {
        $matching = [];
        foreach ($this->Styles as $style) {
            if (fnmatch($pattern, $style)) {
                $matching[] = $style;
            }
        }
        return $matching;
    }

    /**
     * Check if a style exists.
     */
    public function hasStyle(string $styleName): bool
    {
        return in_array($styleName, $this->Styles);
    }

    // Legacy getters/setters
    public function readFileName(): string { return $this->_filename; }
    public function writeFileName(string $value): void { $this->FileName = $value; }
    public function defaultFileName(): string { return ''; }

    public function readIncludeStandard(): bool { return $this->_inclstandard; }
    public function writeIncludeStandard(bool $value): void { $this->IncludeStandard = $value; }
    public function defaultIncludeStandard(): bool { return false; }

    public function readIncludeID(): bool { return $this->_inclid; }
    public function writeIncludeID(bool $value): void { $this->IncludeID = $value; }
    public function defaultIncludeID(): bool { return false; }

    public function readIncludeSubStyle(): bool { return $this->_incsubstyle; }
    public function writeIncludeSubStyle(bool $value): void { $this->IncludeSubStyle = $value; }
    public function defaultIncludeSubStyle(): bool { return false; }

    public function readStyles(): array { return $this->Styles; }
    public function writeStyles(array $value): void { $this->Styles = $value; }
}
