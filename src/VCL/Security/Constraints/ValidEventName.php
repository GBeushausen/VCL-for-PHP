<?php

declare(strict_types=1);

namespace VCL\Security\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Constraint for validating VCL event names.
 *
 * Event names must:
 * - Start with a letter or underscore
 * - Contain only alphanumeric characters and underscores
 * - Optionally be in a whitelist of allowed events
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ValidEventName extends Constraint
{
    public string $message = 'The event name "{{ value }}" is invalid.';
    public string $patternMessage = 'The event name "{{ value }}" contains invalid characters.';
    public string $notAllowedMessage = 'The event name "{{ value }}" is not a recognized event.';

    /**
     * Default allowed event names in VCL.
     */
    public const DEFAULT_EVENTS = [
        'onClick',
        'onChange',
        'onSubmit',
        'onFocus',
        'onBlur',
        'onKeyDown',
        'onKeyUp',
        'onKeyPress',
        'onMouseOver',
        'onMouseOut',
        'onMouseDown',
        'onMouseUp',
        'onLoad',
        'onUnload',
        'onBeforeAjaxProcess',
        'onAfterAjaxProcess',
        'onShow',
        'onHide',
        'onActivate',
        'onDeactivate',
    ];

    /**
     * @param array $allowedEvents Whitelist of allowed event names (uses DEFAULT_EVENTS if empty)
     * @param bool $strict If true, the name must be in allowedEvents
     */
    public function __construct(
        public array $allowedEvents = [],
        public bool $strict = false,
        ?string $message = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct([], $groups, $payload);

        if ($message !== null) {
            $this->message = $message;
        }

        // Use default events if none provided
        if (empty($this->allowedEvents)) {
            $this->allowedEvents = self::DEFAULT_EVENTS;
        }
    }
}
