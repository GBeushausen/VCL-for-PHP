/**
 * VCL htmx Helper Functions
 *
 * This file provides VCL-specific extensions and helper functions for htmx integration.
 * It replaces the legacy xajax functionality with modern htmx patterns.
 */

(function() {
    'use strict';

    // VCL namespace
    window.VCL = window.VCL || {};
    window.VCL.htmx = {};

    /**
     * Submit a VCL form via htmx.
     *
     * @param {string} formName - The VCL form name
     * @param {string} controlName - The control that triggered the event
     * @param {string} eventName - The PHP event handler name
     * @param {string} targetSelector - Optional target selector for the response
     */
    VCL.htmx.submit = function(formName, controlName, eventName, targetSelector) {
        var form = document.getElementById(formName + '_form');
        if (!form) {
            console.error('VCL Form not found:', formName);
            return;
        }

        var formData = new FormData(form);
        formData.append('_vcl_form', formName);
        formData.append('_vcl_control', controlName);
        formData.append('_vcl_event', eventName);

        var config = {
            values: Object.fromEntries(formData)
        };

        if (targetSelector) {
            config.target = targetSelector;
        }

        htmx.ajax('POST', form.action || window.location.href, config);
    };

    /**
     * Trigger a VCL event via htmx without form submission.
     *
     * @param {string} formName - The VCL form name
     * @param {string} controlName - The control name
     * @param {string} eventName - The PHP event handler name
     * @param {object} params - Additional parameters to send
     * @param {string} targetSelector - Optional target selector
     */
    VCL.htmx.triggerEvent = function(formName, controlName, eventName, params, targetSelector) {
        var values = {
            '_vcl_form': formName,
            '_vcl_control': controlName,
            '_vcl_event': eventName
        };

        if (params) {
            Object.assign(values, params);
        }

        var config = {
            values: values
        };

        if (targetSelector) {
            config.target = targetSelector;
        }

        htmx.ajax('POST', window.location.href, config);
    };

    /**
     * Update a specific element with htmx.
     *
     * @param {string} selector - The target selector
     * @param {string} html - The HTML content
     * @param {string} swapStyle - The swap style (innerHTML, outerHTML, etc.)
     */
    VCL.htmx.update = function(selector, html, swapStyle) {
        var element = document.querySelector(selector);
        if (!element) {
            console.error('Element not found:', selector);
            return;
        }

        swapStyle = swapStyle || 'innerHTML';
        htmx.swap(element, html, {swapStyle: swapStyle});
    };

    /**
     * Show a loading indicator on an element.
     *
     * @param {string} selector - The element selector
     */
    VCL.htmx.showLoading = function(selector) {
        var element = document.querySelector(selector);
        if (element) {
            element.classList.add('htmx-request');
        }
    };

    /**
     * Hide the loading indicator on an element.
     *
     * @param {string} selector - The element selector
     */
    VCL.htmx.hideLoading = function(selector) {
        var element = document.querySelector(selector);
        if (element) {
            element.classList.remove('htmx-request');
        }
    };

    /**
     * Initialize htmx event listeners.
     * Called when DOM is ready.
     */
    function initHtmxListeners() {
        if (typeof htmx === 'undefined') {
            return;
        }

        // Add VCL metadata to all htmx requests
        document.body.addEventListener('htmx:configRequest', function(event) {
            if (!event.detail.parameters['_vcl_form']) {
                var form = event.detail.elt.closest('form[id$="_form"]');
                if (form) {
                    var formName = form.id.replace('_form', '');
                    event.detail.parameters['_vcl_form'] = formName;
                }
            }
        });

        // Handle htmx errors
        document.body.addEventListener('htmx:responseError', function(event) {
            console.error('htmx response error:', event.detail.xhr.status, event.detail.xhr.statusText);
        });

        // Handle htmx after swap for custom processing
        document.body.addEventListener('htmx:afterSwap', function(event) {
            var scripts = event.detail.target.querySelectorAll('script');
            scripts.forEach(function(script) {
                if (!script.hasAttribute('data-executed')) {
                    var newScript = document.createElement('script');
                    newScript.textContent = script.textContent;
                    script.setAttribute('data-executed', 'true');
                    document.head.appendChild(newScript).parentNode.removeChild(newScript);
                }
            });
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHtmxListeners);
    } else {
        // DOM already loaded
        initHtmxListeners();
    }

    // Backward compatibility with xajax patterns
    window.xajax_ajaxProcess = function(formName, controlName, params, eventName, formValues, components) {
        console.warn('xajax_ajaxProcess is deprecated. Use VCL.htmx.submit() instead.');

        var values = {
            '_vcl_form': formName,
            '_vcl_control': controlName,
            '_vcl_event': eventName
        };

        if (formValues && typeof formValues === 'object') {
            Object.assign(values, formValues);
        }

        if (params) {
            values['params'] = JSON.stringify(params);
        }

        htmx.ajax('POST', window.location.href, {values: values});
    };

    // xajax compatibility shim
    window.xajax = window.xajax || {
        getFormValues: function(formName) {
            var form = document.getElementById(formName + '_form');
            if (!form) return {};

            var formData = new FormData(form);
            return Object.fromEntries(formData);
        }
    };

})();
