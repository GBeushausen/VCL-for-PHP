<?php

declare(strict_types=1);

namespace VCL\Ajax;

use VCL\Core\Component;

/**
 * HtmxHandler processes htmx AJAX requests and generates responses.
 *
 * htmx is a modern replacement for xajax that uses HTML attributes for AJAX behavior.
 * It returns HTML fragments instead of XML/JSON commands.
 *
 * @see https://htmx.org/
 */
class HtmxHandler
{
    private ?Component $owner = null;
    private bool $debug = false;
    private string $requestUri = '';

    /**
     * Create a new HtmxHandler.
     */
    public function __construct(?Component $owner = null)
    {
        $this->owner = $owner;
        $this->requestUri = $_SERVER['REQUEST_URI'] ?? '';
    }

    /**
     * Check if the current request is an htmx request.
     */
    public static function isHtmxRequest(): bool
    {
        return isset($_SERVER['HTTP_HX_REQUEST']) && $_SERVER['HTTP_HX_REQUEST'] === 'true';
    }

    /**
     * Check if this is a boosted request (full page with htmx boost).
     */
    public static function isBoostedRequest(): bool
    {
        return isset($_SERVER['HTTP_HX_BOOSTED']) && $_SERVER['HTTP_HX_BOOSTED'] === 'true';
    }

    /**
     * Get the htmx trigger element ID.
     */
    public static function getTrigger(): ?string
    {
        return $_SERVER['HTTP_HX_TRIGGER'] ?? null;
    }

    /**
     * Get the htmx trigger name.
     */
    public static function getTriggerName(): ?string
    {
        return $_SERVER['HTTP_HX_TRIGGER_NAME'] ?? null;
    }

    /**
     * Get the target element ID.
     */
    public static function getTarget(): ?string
    {
        return $_SERVER['HTTP_HX_TARGET'] ?? null;
    }

    /**
     * Get the current URL that htmx was loaded from.
     */
    public static function getCurrentUrl(): ?string
    {
        return $_SERVER['HTTP_HX_CURRENT_URL'] ?? null;
    }

    /**
     * Set the owner component.
     */
    public function setOwner(Component $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * Enable debug mode.
     */
    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    /**
     * Process an htmx request.
     *
     * @return bool True if request was processed, false otherwise
     */
    public function processRequest(): bool
    {
        if (!self::isHtmxRequest()) {
            return false;
        }

        $controlName = $_POST['_vcl_control'] ?? '';
        $eventName = $_POST['_vcl_event'] ?? '';

        if ($controlName === '' || $eventName === '') {
            if ($this->debug) {
                $this->sendError("Missing control or event: control={$controlName}, event={$eventName}");
                return true;
            }
            return false;
        }

        // Use the owner (page) that was passed to the handler
        $form = $this->owner;

        if ($form === null) {
            $this->sendError('No form/page owner set');
            return true;
        }

        // Find the control - first try findComponent
        $control = $form->findComponent($controlName);

        // If not found, try direct property access
        if ($control === null && property_exists($form, $controlName)) {
            $control = $form->$controlName;
        }

        if ($control === null) {
            if ($this->debug) {
                $components = $form->components?->count() ?? 0;
                $this->sendError("Control not found: {$controlName} (form has {$components} components)");
            } else {
                $this->sendError("Control not found: {$controlName}");
            }
            return true;
        }

        // Fire the event
        $result = $control->callEvent($eventName, $_POST);

        // If result is a string, output it as HTML
        if (is_string($result)) {
            $this->sendHtml($result);
        } elseif ($result === null) {
            // Event was called but returned nothing - this is OK for side-effect events
            $this->sendHtml('');
        } else {
            if ($this->debug) {
                $this->sendError("Event {$eventName} returned non-string: " . gettype($result));
            }
        }

        return true;
    }

    /**
     * Send an HTML response.
     */
    public function sendHtml(string $html): void
    {
        header('Content-Type: text/html; charset=UTF-8');
        echo $html;
    }

    /**
     * Send an error response.
     */
    public function sendError(string $message, int $statusCode = 400): void
    {
        http_response_code($statusCode);
        header('Content-Type: text/html; charset=UTF-8');
        echo '<div class="vcl-htmx-error">' . htmlspecialchars($message) . '</div>';
    }

    /**
     * Send a redirect response via htmx.
     */
    public function sendRedirect(string $url): void
    {
        header('HX-Redirect: ' . $url);
    }

    /**
     * Send a refresh response (reload current page).
     */
    public function sendRefresh(): void
    {
        header('HX-Refresh: true');
    }

    /**
     * Trigger a client-side event.
     */
    public function triggerEvent(string $eventName, array $detail = []): void
    {
        if (empty($detail)) {
            header('HX-Trigger: ' . $eventName);
        } else {
            header('HX-Trigger: ' . json_encode([$eventName => $detail]));
        }
    }

    /**
     * Trigger multiple client-side events after settle.
     */
    public function triggerAfterSettle(array $events): void
    {
        header('HX-Trigger-After-Settle: ' . json_encode($events));
    }

    /**
     * Push a URL to the browser history.
     */
    public function pushUrl(string $url): void
    {
        header('HX-Push-Url: ' . $url);
    }

    /**
     * Replace the current URL in browser history.
     */
    public function replaceUrl(string $url): void
    {
        header('HX-Replace-Url: ' . $url);
    }

    /**
     * Retarget the response to a different element.
     */
    public function retarget(string $selector): void
    {
        header('HX-Retarget: ' . $selector);
    }

    /**
     * Change the swap method for the response.
     */
    public function reswap(string $swapMethod): void
    {
        header('HX-Reswap: ' . $swapMethod);
    }

    // =========================================================================
    // RESPONSE BUILDERS (similar to xajaxResponse)
    // =========================================================================

    /**
     * Create a response that updates an element's innerHTML.
     */
    public static function updateElement(string $elementId, string $html): string
    {
        return sprintf(
            '<div id="%s" hx-swap-oob="innerHTML">%s</div>',
            htmlspecialchars($elementId),
            $html
        );
    }

    /**
     * Create a response that replaces an element entirely.
     */
    public static function replaceElement(string $elementId, string $html): string
    {
        return sprintf(
            '<div id="%s" hx-swap-oob="outerHTML">%s</div>',
            htmlspecialchars($elementId),
            $html
        );
    }

    /**
     * Create a response that appends to an element.
     */
    public static function appendToElement(string $elementId, string $html): string
    {
        return sprintf(
            '<div hx-swap-oob="beforeend:#%s">%s</div>',
            htmlspecialchars($elementId),
            $html
        );
    }

    /**
     * Create a response that prepends to an element.
     */
    public static function prependToElement(string $elementId, string $html): string
    {
        return sprintf(
            '<div hx-swap-oob="afterbegin:#%s">%s</div>',
            htmlspecialchars($elementId),
            $html
        );
    }

    /**
     * Create a response that removes an element.
     */
    public static function removeElement(string $elementId): string
    {
        return sprintf(
            '<div id="%s" hx-swap-oob="delete"></div>',
            htmlspecialchars($elementId)
        );
    }

    /**
     * Create a script tag that will execute on the client.
     */
    public static function executeScript(string $script): string
    {
        return sprintf('<script>%s</script>', $script);
    }

    /**
     * Create an alert response.
     */
    public static function alert(string $message): string
    {
        return self::executeScript(sprintf('alert(%s);', json_encode($message)));
    }

    /**
     * Create a focus response.
     */
    public static function focus(string $elementId): string
    {
        return self::executeScript(sprintf(
            'document.getElementById(%s)?.focus();',
            json_encode($elementId)
        ));
    }

    /**
     * Set a form field value.
     */
    public static function setValue(string $elementId, string $value): string
    {
        return self::executeScript(sprintf(
            'var el = document.getElementById(%s); if(el) el.value = %s;',
            json_encode($elementId),
            json_encode($value)
        ));
    }
}
