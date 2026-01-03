<?php

declare(strict_types=1);

namespace VCL\Security;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * HTML Sanitizer wrapper for XSS prevention.
 *
 * This class provides safe HTML sanitization using Symfony's HtmlSanitizer.
 * It offers different sanitization levels for various use cases.
 *
 * Usage:
 *   // Static facade
 *   $safe = Sanitizer::sanitize($userHtml);
 *   $richText = Sanitizer::sanitizeRichText($userHtml);
 *
 *   // Instance methods
 *   $sanitizer = new Sanitizer();
 *   $safe = $sanitizer->clean($userHtml);
 */
class Sanitizer
{
    private static ?self $instance = null;

    private HtmlSanitizer $strictSanitizer;
    private HtmlSanitizer $richTextSanitizer;
    private HtmlSanitizer $fullSanitizer;

    public function __construct()
    {
        $this->strictSanitizer = $this->createStrictSanitizer();
        $this->richTextSanitizer = $this->createRichTextSanitizer();
        $this->fullSanitizer = $this->createFullSanitizer();
    }

    // =========================================================================
    // INSTANCE METHODS
    // =========================================================================

    /**
     * Sanitize HTML with strict settings (text only, no HTML allowed).
     * All HTML tags are removed, only text content remains.
     *
     * @param string $html The HTML to sanitize
     * @return string Plain text content
     */
    public function clean(string $html): string
    {
        return $this->strictSanitizer->sanitize($html);
    }

    /**
     * Sanitize HTML for rich text content.
     * Allows basic formatting: bold, italic, links, lists, paragraphs.
     *
     * @param string $html The HTML to sanitize
     * @return string Sanitized HTML with allowed formatting
     */
    public function cleanRichText(string $html): string
    {
        return $this->richTextSanitizer->sanitize($html);
    }

    /**
     * Sanitize HTML allowing more elements (for trusted but untrusted content).
     * Includes tables, images, headers, etc. but blocks scripts.
     *
     * @param string $html The HTML to sanitize
     * @return string Sanitized HTML
     */
    public function cleanFull(string $html): string
    {
        return $this->fullSanitizer->sanitize($html);
    }

    /**
     * Sanitize HTML with a custom configuration.
     *
     * @param string $html The HTML to sanitize
     * @param HtmlSanitizerConfig $config Custom configuration
     * @return string Sanitized HTML
     */
    public function cleanWithConfig(string $html, HtmlSanitizerConfig $config): string
    {
        $sanitizer = new HtmlSanitizer($config);
        return $sanitizer->sanitize($html);
    }

    /**
     * Strip all HTML tags and return plain text.
     *
     * @param string $html The HTML to strip
     * @return string Plain text
     */
    public function stripTags(string $html): string
    {
        // Use strip_tags after sanitization for extra safety
        return strip_tags($this->strictSanitizer->sanitize($html));
    }

    // =========================================================================
    // STATIC FACADE METHODS
    // =========================================================================

    /**
     * Sanitize HTML with strict settings (static).
     *
     * @param string $html The HTML to sanitize
     * @return string Plain text content
     */
    public static function sanitize(string $html): string
    {
        return self::getInstance()->clean($html);
    }

    /**
     * Sanitize HTML for rich text (static).
     *
     * @param string $html The HTML to sanitize
     * @return string Sanitized HTML with formatting
     */
    public static function sanitizeRichText(string $html): string
    {
        return self::getInstance()->cleanRichText($html);
    }

    /**
     * Sanitize HTML with full element support (static).
     *
     * @param string $html The HTML to sanitize
     * @return string Sanitized HTML
     */
    public static function sanitizeFull(string $html): string
    {
        return self::getInstance()->cleanFull($html);
    }

    /**
     * Strip all HTML tags (static).
     *
     * @param string $html The HTML to strip
     * @return string Plain text
     */
    public static function strip(string $html): string
    {
        return self::getInstance()->stripTags($html);
    }

    // =========================================================================
    // SANITIZER FACTORY METHODS
    // =========================================================================

    /**
     * Create a strict sanitizer that removes all HTML.
     */
    private function createStrictSanitizer(): HtmlSanitizer
    {
        $config = (new HtmlSanitizerConfig())
            // Don't allow any elements - just extract text
            ->allowElement('br')  // Allow line breaks
            ->forceHttpsUrls();

        return new HtmlSanitizer($config);
    }

    /**
     * Create a rich text sanitizer for user-generated content.
     */
    private function createRichTextSanitizer(): HtmlSanitizer
    {
        $config = (new HtmlSanitizerConfig())
            ->allowSafeElements()
            // Basic formatting
            ->allowElement('p')
            ->allowElement('br')
            ->allowElement('strong')
            ->allowElement('b')
            ->allowElement('em')
            ->allowElement('i')
            ->allowElement('u')
            ->allowElement('s')
            ->allowElement('strike')
            ->allowElement('sub')
            ->allowElement('sup')
            // Lists
            ->allowElement('ul')
            ->allowElement('ol')
            ->allowElement('li')
            // Links (with restrictions)
            ->allowElement('a', ['href', 'title', 'target'])
            // Block elements
            ->allowElement('blockquote')
            ->allowElement('pre')
            ->allowElement('code')
            // Force HTTPS and restrict link schemes
            ->forceHttpsUrls()
            ->allowLinkSchemes(['https', 'http', 'mailto'])
            ->allowRelativeLinks()
            // Force noopener on links
            ->forceAttribute('a', 'rel', 'noopener noreferrer');

        return new HtmlSanitizer($config);
    }

    /**
     * Create a full sanitizer for trusted but potentially unsafe content.
     */
    private function createFullSanitizer(): HtmlSanitizer
    {
        $config = (new HtmlSanitizerConfig())
            ->allowSafeElements()
            // Include all rich text elements
            ->allowElement('p')
            ->allowElement('br')
            ->allowElement('strong')
            ->allowElement('b')
            ->allowElement('em')
            ->allowElement('i')
            ->allowElement('u')
            ->allowElement('s')
            ->allowElement('strike')
            ->allowElement('sub')
            ->allowElement('sup')
            ->allowElement('ul')
            ->allowElement('ol')
            ->allowElement('li')
            ->allowElement('a', ['href', 'title', 'target'])
            ->allowElement('blockquote')
            ->allowElement('pre')
            ->allowElement('code')
            // Headers
            ->allowElement('h1')
            ->allowElement('h2')
            ->allowElement('h3')
            ->allowElement('h4')
            ->allowElement('h5')
            ->allowElement('h6')
            // Tables
            ->allowElement('table', ['class'])
            ->allowElement('thead')
            ->allowElement('tbody')
            ->allowElement('tfoot')
            ->allowElement('tr')
            ->allowElement('th', ['colspan', 'rowspan'])
            ->allowElement('td', ['colspan', 'rowspan'])
            // Other block elements
            ->allowElement('div', ['class'])
            ->allowElement('span', ['class'])
            ->allowElement('hr')
            // Images (with restrictions)
            ->allowElement('img', ['src', 'alt', 'title', 'width', 'height'])
            // Definition lists
            ->allowElement('dl')
            ->allowElement('dt')
            ->allowElement('dd')
            // Force HTTPS
            ->forceHttpsUrls()
            ->allowLinkSchemes(['https', 'http', 'mailto'])
            ->allowMediaSchemes(['https', 'http'])
            ->allowRelativeLinks()
            ->allowRelativeMedias()
            ->forceAttribute('a', 'rel', 'noopener noreferrer');

        return new HtmlSanitizer($config);
    }

    // =========================================================================
    // INSTANCE MANAGEMENT
    // =========================================================================

    /**
     * Get the singleton instance.
     */
    private static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * Set a custom instance (for testing).
     */
    public static function setInstance(?self $instance): void
    {
        self::$instance = $instance;
    }

    /**
     * Reset to default instance.
     */
    public static function resetInstance(): void
    {
        self::$instance = null;
    }

    // =========================================================================
    // CONFIGURATION BUILDERS
    // =========================================================================

    /**
     * Create a custom sanitizer configuration builder.
     * Use this when you need fine-grained control.
     *
     * @return HtmlSanitizerConfig
     */
    public static function createConfig(): HtmlSanitizerConfig
    {
        return new HtmlSanitizerConfig();
    }

    /**
     * Create a sanitizer with a custom configuration.
     *
     * @param HtmlSanitizerConfig $config
     * @return HtmlSanitizer
     */
    public static function createCustomSanitizer(HtmlSanitizerConfig $config): HtmlSanitizer
    {
        return new HtmlSanitizer($config);
    }
}
