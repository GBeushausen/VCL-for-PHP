/**
 * VCL Canvas - HTML5 Canvas Wrapper for VCL for PHP
 *
 * Replaces the legacy wz_jsgraphics.js library with native HTML5 Canvas API.
 * Provides a compatible API that works with the existing PHP Canvas class.
 *
 * @version 1.0.0
 * @license LGPL-2.1-or-later
 */

'use strict';

/**
 * VCLCanvas class - wraps HTML5 Canvas with jsGraphics-compatible API
 */
class VCLCanvas {
    /**
     * Create a VCLCanvas instance
     * @param {string|HTMLElement} target - Canvas element ID or element reference
     */
    constructor(target) {
        if (typeof target === 'string') {
            this.canvas = document.getElementById(target);
            if (!this.canvas) {
                // Try to find as child canvas within container
                const container = document.getElementById(target);
                if (container) {
                    this.canvas = container.querySelector('canvas') || this._createCanvas(container);
                }
            }
        } else if (target instanceof HTMLElement) {
            if (target.tagName === 'CANVAS') {
                this.canvas = target;
            } else {
                this.canvas = target.querySelector('canvas') || this._createCanvas(target);
            }
        }

        if (!this.canvas || !(this.canvas instanceof HTMLCanvasElement)) {
            console.error('VCLCanvas: Could not find or create canvas element for:', target);
            return;
        }

        this.ctx = this.canvas.getContext('2d');
        this._color = '#000000';
        this._strokeWidth = 1;
        this._dotted = false;
        this._fontFamily = 'Verdana, Geneva, Helvetica, sans-serif';
        this._fontSize = '12px';
        this._fontStyle = '';
    }

    /**
     * Create a canvas element within a container
     * @param {HTMLElement} container - Container element
     * @returns {HTMLCanvasElement} Created canvas
     * @private
     */
    _createCanvas(container) {
        const canvas = document.createElement('canvas');
        canvas.width = container.offsetWidth || 100;
        canvas.height = container.offsetHeight || 100;
        canvas.style.position = 'absolute';
        canvas.style.left = '0';
        canvas.style.top = '0';
        container.style.position = 'relative';
        container.appendChild(canvas);
        return canvas;
    }

    /**
     * Set the drawing color
     * @param {string} color - Color in hex format (#RRGGBB)
     */
    setColor(color) {
        this._color = color.toLowerCase();
        this.ctx.fillStyle = this._color;
        this.ctx.strokeStyle = this._color;
    }

    /**
     * Set the stroke width
     * @param {number} width - Stroke width in pixels, -1 for dotted
     */
    setStroke(width) {
        if (width === -1) {
            this._dotted = true;
            this._strokeWidth = 1;
            this.ctx.setLineDash([2, 2]);
        } else {
            this._dotted = false;
            this._strokeWidth = width;
            this.ctx.setLineDash([]);
        }
        this.ctx.lineWidth = this._strokeWidth;
    }

    /**
     * Set the font for text rendering
     * @param {string} family - Font family
     * @param {string} size - Font size (e.g., '12px')
     * @param {string} style - Font style (e.g., 'font-weight:bold;')
     */
    setFont(family, size, style) {
        this._fontFamily = family || this._fontFamily;
        this._fontSize = size || this._fontSize;
        this._fontStyle = style || '';

        // Parse style string to Canvas font format
        let fontString = '';

        if (this._fontStyle.includes('italic')) {
            fontString += 'italic ';
        }
        if (this._fontStyle.includes('bold')) {
            fontString += 'bold ';
        }

        fontString += this._fontSize + ' ' + this._fontFamily;
        this.ctx.font = fontString;
    }

    /**
     * Draw a line
     * @param {number} x1 - Start X
     * @param {number} y1 - Start Y
     * @param {number} x2 - End X
     * @param {number} y2 - End Y
     */
    drawLine(x1, y1, x2, y2) {
        this.ctx.beginPath();
        this.ctx.moveTo(x1, y1);
        this.ctx.lineTo(x2, y2);
        this.ctx.stroke();
    }

    /**
     * Draw a rectangle outline
     * @param {number} x - X position
     * @param {number} y - Y position
     * @param {number} w - Width
     * @param {number} h - Height
     */
    drawRect(x, y, w, h) {
        this.ctx.strokeRect(x, y, w, h);
    }

    /**
     * Fill a rectangle
     * @param {number} x - X position
     * @param {number} y - Y position
     * @param {number} w - Width
     * @param {number} h - Height
     */
    fillRect(x, y, w, h) {
        this.ctx.fillRect(x, y, w, h);
    }

    /**
     * Draw an ellipse outline
     * @param {number} x - X position
     * @param {number} y - Y position
     * @param {number} w - Width
     * @param {number} h - Height
     */
    drawEllipse(x, y, w, h) {
        const cx = x + w / 2;
        const cy = y + h / 2;
        const rx = w / 2;
        const ry = h / 2;

        this.ctx.beginPath();
        this.ctx.ellipse(cx, cy, rx, ry, 0, 0, 2 * Math.PI);
        this.ctx.stroke();
    }

    /**
     * Alias for drawEllipse
     */
    drawOval(x, y, w, h) {
        this.drawEllipse(x, y, w, h);
    }

    /**
     * Fill an ellipse
     * @param {number} x - X position
     * @param {number} y - Y position
     * @param {number} w - Width
     * @param {number} h - Height
     */
    fillEllipse(x, y, w, h) {
        const cx = x + w / 2;
        const cy = y + h / 2;
        const rx = w / 2;
        const ry = h / 2;

        this.ctx.beginPath();
        this.ctx.ellipse(cx, cy, rx, ry, 0, 0, 2 * Math.PI);
        this.ctx.fill();
    }

    /**
     * Alias for fillEllipse
     */
    fillOval(x, y, w, h) {
        this.fillEllipse(x, y, w, h);
    }

    /**
     * Fill an arc (pie slice)
     * @param {number} x - X position
     * @param {number} y - Y position
     * @param {number} w - Width
     * @param {number} h - Height
     * @param {number} startAngle - Start angle in degrees
     * @param {number} endAngle - End angle in degrees
     */
    fillArc(x, y, w, h, startAngle, endAngle) {
        const cx = x + w / 2;
        const cy = y + h / 2;
        const rx = w / 2;
        const ry = h / 2;

        // Convert degrees to radians
        // jsGraphics uses counter-clockwise angles, HTML5 Canvas uses clockwise by default
        // Negate angles to match the original behavior
        const startRad = (-startAngle * Math.PI) / 180;
        const endRad = (-endAngle * Math.PI) / 180;

        this.ctx.beginPath();
        this.ctx.moveTo(cx, cy);
        // Use counter-clockwise (true) to match jsGraphics behavior
        this.ctx.ellipse(cx, cy, rx, ry, 0, startRad, endRad, true);
        this.ctx.closePath();
        this.ctx.fill();
    }

    /**
     * Draw a polygon outline
     * @param {number[]} xPoints - Array of X coordinates
     * @param {number[]} yPoints - Array of Y coordinates
     */
    drawPolygon(xPoints, yPoints) {
        if (xPoints.length < 2) return;

        this.ctx.beginPath();
        this.ctx.moveTo(xPoints[0], yPoints[0]);

        for (let i = 1; i < xPoints.length; i++) {
            this.ctx.lineTo(xPoints[i], yPoints[i]);
        }

        this.ctx.closePath();
        this.ctx.stroke();
    }

    /**
     * Fill a polygon
     * @param {number[]} xPoints - Array of X coordinates
     * @param {number[]} yPoints - Array of Y coordinates
     */
    fillPolygon(xPoints, yPoints) {
        if (xPoints.length < 2) return;

        this.ctx.beginPath();
        this.ctx.moveTo(xPoints[0], yPoints[0]);

        for (let i = 1; i < xPoints.length; i++) {
            this.ctx.lineTo(xPoints[i], yPoints[i]);
        }

        this.ctx.closePath();
        this.ctx.fill();
    }

    /**
     * Draw a polyline (open polygon)
     * @param {number[]} xPoints - Array of X coordinates
     * @param {number[]} yPoints - Array of Y coordinates
     */
    drawPolyline(xPoints, yPoints) {
        if (xPoints.length < 2) return;

        this.ctx.beginPath();
        this.ctx.moveTo(xPoints[0], yPoints[0]);

        for (let i = 1; i < xPoints.length; i++) {
            this.ctx.lineTo(xPoints[i], yPoints[i]);
        }

        this.ctx.stroke();
    }

    /**
     * Draw text
     * @param {string} text - Text to draw
     * @param {number} x - X position
     * @param {number} y - Y position
     */
    drawString(text, x, y) {
        // Adjust Y position - Canvas measures from baseline, jsGraphics from top
        const height = parseInt(this._fontSize, 10) || 12;
        this.ctx.fillText(text, x, y + height);
    }

    /**
     * Draw text in a rectangle with alignment
     * @param {string} text - Text to draw
     * @param {number} x - X position
     * @param {number} y - Y position
     * @param {number} w - Width
     * @param {string} align - Alignment ('left', 'center', 'right')
     */
    drawStringRect(text, x, y, w, align) {
        const oldAlign = this.ctx.textAlign;

        switch (align) {
            case 'center':
                this.ctx.textAlign = 'center';
                this.drawString(text, x + w / 2, y);
                break;
            case 'right':
                this.ctx.textAlign = 'right';
                this.drawString(text, x + w, y);
                break;
            default:
                this.ctx.textAlign = 'left';
                this.drawString(text, x, y);
        }

        this.ctx.textAlign = oldAlign;
    }

    /**
     * Draw an image
     * @param {string} src - Image source URL
     * @param {number} x - X position
     * @param {number} y - Y position
     * @param {number} w - Width (optional)
     * @param {number} h - Height (optional)
     */
    drawImage(src, x, y, w, h) {
        const img = new Image();
        const ctx = this.ctx;

        img.onload = function() {
            if (w !== undefined && h !== undefined) {
                ctx.drawImage(img, x, y, w, h);
            } else {
                ctx.drawImage(img, x, y);
            }
        };

        img.src = src;
    }

    /**
     * Clear the canvas
     */
    clear() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
    }

    /**
     * Paint/render the canvas (no-op for HTML5 Canvas - renders immediately)
     * Kept for API compatibility with jsGraphics
     */
    paint() {
        // HTML5 Canvas renders immediately, no buffering needed
    }

    /**
     * Set printable mode (no-op for HTML5 Canvas)
     * Kept for API compatibility with jsGraphics
     * @param {boolean} printable - Enable printable mode
     */
    setPrintable(printable) {
        // HTML5 Canvas prints natively
    }
}

/**
 * Font style constants (jsGraphics compatibility)
 */
const Font = {
    PLAIN: '',
    BOLD: 'font-weight:bold;',
    ITALIC: 'font-style:italic;',
    ITALIC_BOLD: 'font-style:italic;font-weight:bold;',
    BOLD_ITALIC: 'font-style:italic;font-weight:bold;'
};

/**
 * Stroke style constants (jsGraphics compatibility)
 */
const Stroke = {
    DOTTED: -1
};

/**
 * Factory function for backwards compatibility
 * Creates a VCLCanvas instance (replaces `new jsGraphics()`)
 * @param {string|HTMLElement} target - Target element
 * @returns {VCLCanvas} Canvas instance
 */
function jsGraphics(target) {
    return new VCLCanvas(target);
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { VCLCanvas, jsGraphics, Font, Stroke };
}

// Attach to window for backwards compatibility with wz_jsgraphics.js globals
// In strict mode, const declarations are not added to the global object
if (typeof window !== 'undefined') {
    window.VCLCanvas = VCLCanvas;
    window.jsGraphics = jsGraphics;
    window.Font = window.Font || Font;
    window.Stroke = window.Stroke || Stroke;
}
