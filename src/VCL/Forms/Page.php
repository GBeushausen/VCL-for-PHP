<?php

declare(strict_types=1);

namespace VCL\Forms;

use VCL\Forms\Enums\DocType;
use VCL\Forms\Enums\Directionality;
use VCL\Forms\Enums\FrameBorder;
use VCL\Graphics\Enums\LayoutType;

/**
 * Page is the main page component representing a browser page.
 *
 * Provides comprehensive control over HTML page generation including headers,
 * forms, JavaScript, encoding, and layout.
 *
 * PHP 8.4 version with Property Hooks.
 */
class Page extends CustomPage
{
    // Header/Footer
    protected bool $_showheader = true;
    protected bool $_showfooter = true;
    protected bool $_ismaster = false;

    // Margins
    protected int $_marginwidth = 0;
    protected int $_marginheight = 0;
    protected int $_leftmargin = 0;
    protected int $_topmargin = 0;
    protected int $_rightmargin = 0;
    protected int $_bottommargin = 0;

    // Htmx (AJAX support)
    protected bool $_usehtmx = false;
    protected bool $_usehtmxdebug = false;

    // Tailwind CSS support
    protected bool $_useTailwind = false;
    protected string $_tailwindStylesheet = '';
    protected array $_bodyClasses = [];
    protected string $_defaultTheme = 'light';

    // Template
    protected bool $_dynamic = false;
    protected string $_templateengine = '';
    protected string $_templatefilename = '';

    // Form
    protected bool $_isform = true;
    protected string $_action = '';
    protected string $_formencoding = '';

    // Document
    protected DocType|string $_doctype = DocType::HTML5;
    protected string $_encoding = 'UTF-8|utf-8';
    protected Directionality|string $_directionality = Directionality::LeftToRight;

    // Appearance
    protected string $_background = '';
    protected string $_icon = '';
    protected string $_language = '(default)';

    // Frames
    protected int $_framespacing = 0;
    protected FrameBorder|string $_frameborder = FrameBorder::No;
    protected int $_borderwidth = 0;
    protected string $_border = '';
    protected bool $hasframes = false;

    // Events
    protected ?string $_onbeforeshowheader = null;
    protected ?string $_onstartbody = null;
    protected ?string $_onshowheader = null;
    protected ?string $_onaftershowfooter = null;
    protected ?string $_oncreate = null;
    protected ?string $_onbeforeajaxprocess = null;

    // JavaScript Events
    protected ?string $_jsonload = null;
    protected ?string $_jsonunload = null;

    // =========================================================================
    // PROPERTY HOOKS
    // =========================================================================

    public bool $ShowHeader {
        get => $this->_showheader;
        set => $this->_showheader = $value;
    }

    public bool $ShowFooter {
        get => $this->_showfooter;
        set => $this->_showfooter = $value;
    }

    public bool $IsMaster {
        get => $this->_ismaster;
        set => $this->_ismaster = $value;
    }

    public int $MarginWidth {
        get => $this->_marginwidth;
        set => $this->_marginwidth = $value;
    }

    public int $MarginHeight {
        get => $this->_marginheight;
        set => $this->_marginheight = $value;
    }

    public int $LeftMargin {
        get => $this->_leftmargin;
        set => $this->_leftmargin = $value;
    }

    public int $TopMargin {
        get => $this->_topmargin;
        set => $this->_topmargin = $value;
    }

    public int $RightMargin {
        get => $this->_rightmargin;
        set => $this->_rightmargin = $value;
    }

    public int $BottomMargin {
        get => $this->_bottommargin;
        set => $this->_bottommargin = $value;
    }

    public bool $UseHtmx {
        get => $this->_usehtmx;
        set => $this->_usehtmx = $value;
    }

    public bool $UseHtmxDebug {
        get => $this->_usehtmxdebug;
        set => $this->_usehtmxdebug = $value;
    }

    public bool $UseTailwind {
        get => $this->_useTailwind;
        set => $this->_useTailwind = $value;
    }

    public string $TailwindStylesheet {
        get => $this->_tailwindStylesheet;
        set => $this->_tailwindStylesheet = $value;
    }

    public array $BodyClasses {
        get => $this->_bodyClasses;
        set => $this->_bodyClasses = $value;
    }

    public string $DefaultTheme {
        get => $this->_defaultTheme;
        set => $this->_defaultTheme = $value;
    }

    public bool $Dynamic {
        get => $this->_dynamic;
        set => $this->_dynamic = $value;
    }

    public string $TemplateEngine {
        get => $this->_templateengine;
        set => $this->_templateengine = $value;
    }

    public string $TemplateFilename {
        get => $this->_templatefilename;
        set => $this->_templatefilename = $value;
    }

    public bool $IsForm {
        get => $this->_isform;
        set => $this->_isform = $value;
    }

    public string $Action {
        get => $this->_action;
        set => $this->_action = $value;
    }

    public string $FormEncoding {
        get => $this->_formencoding;
        set => $this->_formencoding = $value;
    }

    public DocType|string $DocType {
        get => $this->_doctype;
        set => $this->_doctype = $value instanceof DocType ? $value : DocType::from($value);
    }

    public string $Encoding {
        get => $this->_encoding;
        set => $this->_encoding = $value;
    }

    public Directionality|string $Directionality {
        get => $this->_directionality;
        set => $this->_directionality = $value instanceof Directionality ? $value : Directionality::from($value);
    }

    public string $Background {
        get => $this->_background;
        set => $this->_background = $value;
    }

    public string $Icon {
        get => $this->_icon;
        set => $this->_icon = $value;
    }

    public string $Language {
        get => $this->_language;
        set => $this->_language = $value;
    }

    public int $FrameSpacing {
        get => $this->_framespacing;
        set => $this->_framespacing = $value;
    }

    public FrameBorder|string $FrameBorder {
        get => $this->_frameborder;
        set => $this->_frameborder = $value instanceof FrameBorder ? $value : FrameBorder::from($value);
    }

    public int $BorderWidth {
        get => $this->_borderwidth;
        set => $this->_borderwidth = $value;
    }

    public ?string $jsOnLoad {
        get => $this->_jsonload;
        set => $this->_jsonload = $value;
    }

    public ?string $jsOnUnload {
        get => $this->_jsonunload;
        set => $this->_jsonunload = $value;
    }

    // =========================================================================
    // METHODS
    // =========================================================================

    /**
     * Get the charset from the encoding property.
     */
    public function getCharset(): string
    {
        $parts = explode('|', $this->_encoding);
        return $parts[1] ?? 'utf-8';
    }

    /**
     * Get the start form tag.
     */
    public function readStartForm(): string
    {
        if (!$this->_isform) {
            return '';
        }

        global $scriptfilename;
        $action = $this->_action !== '' ? $this->_action : ($scriptfilename ?? '');

        $enctype = '';
        if ($this->_formencoding !== '') {
            $enctype = sprintf(' enctype="%s"', htmlspecialchars($this->_formencoding));
        }

        $name = $this->Name ?? 'form';

        return sprintf(
            '<form id="%s_form" name="%s_form" method="post" action="%s"%s>',
            htmlspecialchars($name),
            htmlspecialchars($name),
            htmlspecialchars($action),
            $enctype
        );
    }

    /**
     * Get the end form tag.
     */
    public function readEndForm(): string
    {
        if (!$this->_isform) {
            return '';
        }
        return '</form>';
    }

    /**
     * Dump page header.
     */
    public function dumpHeader(): void
    {
        $doctype = $this->_doctype instanceof DocType ? $this->_doctype : DocType::from($this->_doctype);
        $dtd = $doctype->toDeclaration();

        $dir = $this->_directionality instanceof Directionality
            ? $this->_directionality
            : Directionality::from($this->_directionality);

        // Output DOCTYPE
        if ($dtd !== '') {
            echo $dtd . "\n";
        }

        // Build HTML tag attributes
        $htmlAttrs = [];
        $htmlAttrs[] = 'lang="' . $this->getLanguageCode() . '"';
        $htmlAttrs[] = 'dir="' . $dir->toHtmlDir() . '"';

        // Add XHTML namespace if needed
        $extra = $doctype->toHtmlAttributes();
        if ($extra !== '') {
            $htmlAttrs[] = $extra;
        }

        // Add Tailwind theme attribute
        if ($this->_useTailwind) {
            $htmlAttrs[] = sprintf('data-theme="%s"', htmlspecialchars($this->_defaultTheme));
        }

        echo '<html ' . implode(' ', $htmlAttrs) . ">\n";
        echo "<head>\n";

        // HTML5: charset meta tag first
        $charset = $this->getCharset();
        if ($doctype === DocType::HTML5) {
            echo sprintf('<meta charset="%s">' . "\n", $charset);
        } else {
            echo sprintf('<meta http-equiv="Content-Type" content="text/html; charset=%s">' . "\n", $charset);
        }

        // HTML5: Viewport for responsive/autoscale
        if ($doctype === DocType::HTML5) {
            echo '<meta name="viewport" content="width=device-width, initial-scale=1">' . "\n";
        }

        echo sprintf("<title>%s</title>\n", htmlspecialchars($this->Caption));

        // Favicon
        if ($this->_icon !== '') {
            echo sprintf('<link rel="icon" href="%s">' . "\n", htmlspecialchars($this->_icon));
        }

        // Tailwind CSS stylesheet
        if ($this->_useTailwind && $this->_tailwindStylesheet !== '') {
            $stylesheetUrl = $this->pathToUrl($this->_tailwindStylesheet);
            echo sprintf('<link rel="stylesheet" href="%s">' . "\n", htmlspecialchars($stylesheetUrl, ENT_QUOTES, 'UTF-8'));
        }

        $this->callEvent('onshowheader', []);

        // Include htmx library if enabled
        if ($this->_usehtmx) {
            $this->dumpHtmxScript();
        }

        $this->dumpHeaderJavascript();
        $this->dumpChildrenHeaderCode();

        echo "</head>\n";
    }

    /**
     * Dump htmx script include.
     */
    protected function dumpHtmxScript(): void
    {
        global $VCLPATH;
        $basePath = $VCLPATH ?? '';

        // htmx from npm (node_modules)
        $htmxFilePath = $basePath . 'node_modules/htmx.org/dist/htmx.min.js';

        // VCL htmx helper from src/VCL/Assets
        $vclHtmxFilePath = $basePath . 'src/VCL/Assets/js/vcl-htmx.js';

        // Determine URL for htmx: local file if available, otherwise CDN
        // CDN version should match package.json (htmx.org ^2.0.8) - updated 2025-01
        if (file_exists($htmxFilePath)) {
            $htmxUrl = $this->pathToUrl($htmxFilePath);
        } else {
            $htmxUrl = 'https://unpkg.com/htmx.org@2';
        }

        echo sprintf('<script src="%s"></script>' . "\n", htmlspecialchars($htmxUrl, ENT_QUOTES, 'UTF-8'));

        // Include VCL htmx helper if it exists
        if (file_exists($vclHtmxFilePath)) {
            $vclHtmxUrl = $this->pathToUrl($vclHtmxFilePath);
            echo sprintf('<script src="%s"></script>' . "\n", htmlspecialchars($vclHtmxUrl, ENT_QUOTES, 'UTF-8'));
        }

        // Add debug extension if enabled
        if ($this->_usehtmxdebug) {
            echo '<script>htmx.logAll();</script>' . "\n";
        }
    }

    /**
     * Convert a filesystem path to a web-accessible URL.
     */
    protected function pathToUrl(string $path): string
    {
        // If it's already an absolute URL, return as-is
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        $normalizedPath = str_replace('\\', '/', $path);
        $normalizedRoot = $documentRoot !== '' ? str_replace('\\', '/', rtrim($documentRoot, '/\\')) : '';

        $relative = $normalizedPath;
        if ($normalizedRoot !== '' && str_starts_with($normalizedPath, $normalizedRoot)) {
            $relative = substr($normalizedPath, strlen($normalizedRoot));
        }

        if ($relative === '' || $relative[0] !== '/') {
            $relative = '/' . ltrim($relative, '/');
        }

        return $relative;
    }

    /**
     * Get the language code for the HTML lang attribute.
     */
    public function getLanguageCode(): string
    {
        if ($this->_language === '(default)' || $this->_language === '') {
            return 'de';
        }
        // Extract language code from full language name if needed
        $lang = strtolower($this->_language);
        return match(true) {
            str_contains($lang, 'german') || str_contains($lang, 'deutsch') => 'de',
            str_contains($lang, 'english') => 'en',
            str_contains($lang, 'french') || str_contains($lang, 'français') => 'fr',
            str_contains($lang, 'spanish') || str_contains($lang, 'español') => 'es',
            str_contains($lang, 'italian') || str_contains($lang, 'italiano') => 'it',
            default => substr($lang, 0, 2),
        };
    }

    /**
     * Dump header JavaScript.
     */
    public function dumpHeaderJavascript(bool $returnContents = false): string
    {
        if ($returnContents) {
            ob_start();
        }

        echo "<script type=\"text/javascript\">\n";
        $this->dumpChildrenJavascript();
        echo "</script>\n";

        if ($returnContents) {
            $result = ob_get_contents();
            ob_end_clean();
            return $result;
        }

        return '';
    }

    /**
     * Dump body start.
     */
    public function dumpBodyStart(): void
    {
        $attrs = [];
        $styles = [];
        $classes = [];

        // Body classes (for Tailwind)
        if (!empty($this->_bodyClasses)) {
            $classes = array_merge($classes, $this->_bodyClasses);
        }

        // Margins via CSS (only if not using Tailwind classes for margins)
        if ($this->_leftmargin > 0) {
            $styles[] = "margin-left: {$this->_leftmargin}px";
        }
        if ($this->_topmargin > 0) {
            $styles[] = "margin-top: {$this->_topmargin}px";
        }
        if ($this->_rightmargin > 0) {
            $styles[] = "margin-right: {$this->_rightmargin}px";
        }
        if ($this->_bottommargin > 0) {
            $styles[] = "margin-bottom: {$this->_bottommargin}px";
        }

        // Background color via CSS (HTML5 compliant)
        if ($this->Color !== '') {
            $styles[] = sprintf('background-color: %s', htmlspecialchars($this->Color));
        }

        // Background image via CSS (HTML5 compliant)
        if ($this->_background !== '') {
            $styles[] = sprintf('background-image: url(%s)', htmlspecialchars($this->_background));
            $styles[] = 'background-repeat: no-repeat';
            $styles[] = 'background-size: cover';
        }

        // Class attribute
        if (!empty($classes)) {
            $attrs[] = sprintf('class="%s"', htmlspecialchars(implode(' ', $classes)));
        }

        // Style attribute
        if (!empty($styles)) {
            $attrs[] = 'style="' . implode('; ', $styles) . '"';
        }

        // Event handlers
        if ($this->_jsonload !== null) {
            $attrs[] = sprintf('onload="return %s(event)"', htmlspecialchars($this->_jsonload));
        }

        if ($this->_jsonunload !== null) {
            $attrs[] = sprintf('onunload="return %s(event)"', htmlspecialchars($this->_jsonunload));
        }

        $attrStr = !empty($attrs) ? ' ' . implode(' ', $attrs) : '';
        echo "<body{$attrStr}>\n";
    }

    /**
     * Dump page contents.
     */
    protected function dumpContents(): void
    {
        $this->callEvent('onshow', []);

        if ($this->_ismaster) {
            return;
        }

        // Process htmx AJAX requests
        if ($this->_usehtmx) {
            $this->processHtmx();
        }

        if ($this->_templateengine !== '') {
            $this->dumpUsingTemplate();
            return;
        }

        // Check for frames
        $this->hasframes = false;
        foreach ($this->controls->items as $v) {
            if (method_exists($v, 'inheritsFrom')) {
                if ($v->inheritsFrom('Frame') || $v->inheritsFrom('Frameset')) {
                    $this->hasframes = true;
                    break;
                }
            }
        }

        $this->callEvent('onbeforeshowheader', []);

        if ($this->_showheader) {
            $this->dumpHeader();

            if (!$this->hasframes) {
                $this->dumpBodyStart();
            }
        }

        if (!$this->hasframes) {
            echo $this->readStartForm();
        }

        $this->dumpChildrenFormItems();

        $this->callEvent('onstartbody', []);

        if (!$this->hasframes) {
            $this->dumpChildren();
        } else {
            $this->dumpFrames();
        }

        if ($this->_isform && $this->_showfooter) {
            if (!$this->hasframes) {
                echo $this->readEndForm();
            }
        }

        $this->callEvent('onaftershowfooter', []);

        if (!$this->hasframes) {
            if ($this->_showfooter) {
                // Output Tailwind theme switch script before closing body
                $this->dumpTailwindThemeScript();

                echo "</body>\n";
                echo "</html>\n";
            }
        }
    }

    /**
     * Dump children controls.
     */
    public function dumpChildren(): void
    {
        $layout = $this->Layout;

        // Build container styles
        $styles = [];
        $styles[] = 'position: relative';

        if ($this->Width > 0) {
            $styles[] = "width: {$this->Width}px";
        } else {
            $styles[] = 'width: 100%';
        }

        if ($this->Height > 0) {
            $styles[] = "height: {$this->Height}px";
        } else {
            $styles[] = 'min-height: 100vh';
        }

        if ($this->Color !== '') {
            $styles[] = sprintf('background-color: %s', htmlspecialchars($this->Color));
        }

        $styleStr = implode('; ', $styles);

        echo sprintf("\n<div class=\"vcl-container\" style=\"%s\">\n", $styleStr);

        if (($this->ControlState & CS_DESIGNING) !== CS_DESIGNING) {
            $layout->dumpLayoutContents(['Frame', 'Frameset']);
        }

        echo "</div>\n";

        // Dump layer controls
        foreach ($this->controls->items as $v) {
            if ($v->Visible && property_exists($v, 'IsLayer') && $v->IsLayer) {
                $v->show();
            }
        }
    }

    // dumpChildrenHeaderCode, dumpChildrenJavascript, dumpChildrenFormItems
    // are inherited from Component and work correctly for Page

    /**
     * Dump frames.
     */
    public function dumpFrames(): void
    {
        // Placeholder for frame rendering
        foreach ($this->controls->items as $v) {
            if (method_exists($v, 'inheritsFrom')) {
                if ($v->inheritsFrom('Frame') || $v->inheritsFrom('Frameset')) {
                    $v->show();
                }
            }
        }
    }

    /**
     * Dump using template engine.
     */
    public function dumpUsingTemplate(): void
    {
        // Placeholder for template engine integration
        // Would integrate with Smarty or other template engines
    }

    /**
     * Dump Tailwind CSS theme switch script.
     *
     * This script enables light/dark mode toggling and respects system preferences.
     */
    protected function dumpTailwindThemeScript(): void
    {
        if (!$this->_useTailwind) {
            return;
        }

        echo <<<'SCRIPT'
<script>
(function() {
    const VCLTheme = {
        get: function() {
            return localStorage.getItem('vcl-theme') ||
                   (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        },
        set: function(theme) {
            localStorage.setItem('vcl-theme', theme);
            document.documentElement.setAttribute('data-theme', theme);
        },
        toggle: function() {
            this.set(this.get() === 'dark' ? 'light' : 'dark');
        },
        init: function() {
            const savedTheme = localStorage.getItem('vcl-theme');
            if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
            } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
            // Listen for system theme changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                if (!localStorage.getItem('vcl-theme')) {
                    document.documentElement.setAttribute('data-theme', e.matches ? 'dark' : 'light');
                }
            });
        }
    };
    VCLTheme.init();
    window.VCLTheme = VCLTheme;
})();
</script>

SCRIPT;
    }

    /**
     * Process htmx AJAX requests.
     *
     * This method checks for htmx requests and processes them,
     * returning HTML fragments instead of full page renders.
     */
    public function processHtmx(): void
    {
        if (!\VCL\Ajax\HtmxHandler::isHtmxRequest()) {
            return;
        }

        $this->callEvent('onbeforeajaxprocess', []);

        $handler = new \VCL\Ajax\HtmxHandler($this);
        $handler->setDebug($this->_usehtmxdebug);

        if ($handler->processRequest()) {
            exit; // Stop page processing after htmx response
        }
    }

    /**
     * Override show to render the full page.
     */
    public function show(): void
    {
        if (!$this->canShow()) {
            return;
        }

        $this->dumpContents();
    }

    // =========================================================================
    // LEGACY GETTERS/SETTERS
    // =========================================================================

    public function getShowHeader(): bool { return $this->_showheader; }
    public function setShowHeader(bool $value): void { $this->ShowHeader = $value; }
    public function defaultShowHeader(): int { return 1; }

    public function getShowFooter(): bool { return $this->_showfooter; }
    public function setShowFooter(bool $value): void { $this->ShowFooter = $value; }
    public function defaultShowFooter(): int { return 1; }

    public function getIsMaster(): bool { return $this->_ismaster; }
    public function setIsMaster(bool $value): void { $this->IsMaster = $value; }
    public function defaultIsMaster(): string { return '0'; }

    public function getDocType(): DocType|string { return $this->_doctype; }
    public function setDocType(DocType|string $value): void { $this->DocType = $value; }
    public function defaultDocType(): string { return 'dtHTML5'; }

    public function getEncoding(): string { return $this->_encoding; }
    public function setEncoding(string $value): void { $this->Encoding = $value; }
    public function defaultEncoding(): string { return 'UTF-8|utf-8'; }

    public function readFormEncoding(): string { return $this->_formencoding; }
    public function writeFormEncoding(string $value): void { $this->FormEncoding = $value; }
    public function defaultFormEncoding(): string { return ''; }

    public function getBackground(): string { return $this->_background; }
    public function setBackground(string $value): void { $this->Background = $value; }
    public function defaultBackground(): string { return ''; }

    public function getTemplateEngine(): string { return $this->_templateengine; }
    public function setTemplateEngine(string $value): void { $this->TemplateEngine = $value; }
    public function defaultTemplateEngine(): string { return ''; }

    public function getTemplateFilename(): string { return $this->_templatefilename; }
    public function setTemplateFilename(string $value): void { $this->TemplateFilename = $value; }
    public function defaultTemplateFilename(): string { return ''; }

    public function getAction(): string { return $this->_action; }
    public function setAction(string $value): void { $this->Action = $value; }
    public function defaultAction(): string { return ''; }

    public function getIcon(): string { return $this->_icon; }
    public function setIcon(string $value): void { $this->Icon = $value; }
    public function defaultIcon(): string { return ''; }

    public function getFrameSpacing(): int { return $this->_framespacing; }
    public function setFrameSpacing(int $value): void { $this->FrameSpacing = $value; }
    public function defaultFrameSpacing(): int { return 0; }

    public function getFrameBorder(): FrameBorder|string { return $this->_frameborder; }
    public function setFrameBorder(FrameBorder|string $value): void { $this->FrameBorder = $value; }
    public function defaultFrameBorder(): string { return 'fbNo'; }

    public function getBorderWidth(): int { return $this->_borderwidth; }
    public function setBorderWidth(int $value): void { $this->BorderWidth = $value; }
    public function defaultBorderWidth(): int { return 0; }

    public function getjsOnLoad(): ?string { return $this->_jsonload; }
    public function setjsOnLoad(?string $value): void { $this->jsOnLoad = $value; }
    public function defaultjsOnLoad(): ?string { return null; }

    public function getjsOnUnload(): ?string { return $this->_jsonunload; }
    public function setjsOnUnload(?string $value): void { $this->jsOnUnload = $value; }
    public function defaultjsOnUnload(): ?string { return null; }

    public function getUseHtmx(): bool { return $this->_usehtmx; }
    public function setUseHtmx(bool $value): void { $this->UseHtmx = $value; }
    public function defaultUseHtmx(): bool { return false; }

    public function getUseHtmxDebug(): bool { return $this->_usehtmxdebug; }
    public function setUseHtmxDebug(bool $value): void { $this->UseHtmxDebug = $value; }
    public function defaultUseHtmxDebug(): bool { return false; }

    public function getUseTailwind(): bool { return $this->_useTailwind; }
    public function setUseTailwind(bool $value): void { $this->UseTailwind = $value; }
    public function defaultUseTailwind(): bool { return false; }

    public function getTailwindStylesheet(): string { return $this->_tailwindStylesheet; }
    public function setTailwindStylesheet(string $value): void { $this->TailwindStylesheet = $value; }
    public function defaultTailwindStylesheet(): string { return ''; }

    public function getBodyClasses(): array { return $this->_bodyClasses; }
    public function setBodyClasses(array $value): void { $this->BodyClasses = $value; }
    public function defaultBodyClasses(): array { return []; }

    public function getDefaultTheme(): string { return $this->_defaultTheme; }
    public function setDefaultTheme(string $value): void { $this->DefaultTheme = $value; }
    public function defaultDefaultTheme(): string { return 'light'; }
}
