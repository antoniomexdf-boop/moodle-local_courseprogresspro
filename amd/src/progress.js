// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Course progress widget renderer.
 *
 * @module     local_courseprogresspro/progress
 * @copyright  2026 Jesus Antonio Jimenez Avina <antoniomexdf@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/templates'], function(Templates) {

    /**
     * Get a string configuration value.
     *
     * @param {Object} config Widget configuration.
     * @param {string} key Configuration key.
     * @returns {string} The configuration string value or empty string.
     */
    function getConfigString(config, key) {
        return config && config[key] ? config[key] : '';
    }

    /**
     * Get whether a numeric config flag is enabled.
     *
     * @param {Object} config Widget configuration.
     * @param {string} key Configuration key.
     * @param {boolean} fallback Default value when key is absent.
     * @returns {boolean} True when the flag value equals 1.
     */
    function getConfigFlag(config, key, fallback) {
        if (!config || typeof config[key] === 'undefined') {
            return fallback;
        }

        return Number(config[key]) === 1;
    }

    /**
     * Get or create the root widget container.
     *
     * @returns {HTMLElement} The existing or newly created container element.
     */
    function getOrCreateContainer() {
        var container = document.getElementById('local-courseprogresspro');
        if (container) {
            return container;
        }

        container = document.createElement('div');
        container.id = 'local-courseprogresspro';
        container.className = 'local-courseprogresspro';
        container.setAttribute('aria-live', 'polite');
        document.body.insertBefore(container, document.body.firstChild);
        return container;
    }

    /**
     * Move the widget container into the main course content area.
     *
     * @param {HTMLElement} container The widget root element to relocate.
     * @returns {void}
     */
    function moveToCourseContent(container) {
        var selectors = [
            '#region-main',
            '#page-content .container-fluid',
            '#page-content',
            '.course-content'
        ];

        for (var i = 0; i < selectors.length; i++) {
            var target = document.querySelector(selectors[i]);
            if (target) {
                target.insertBefore(container, target.firstChild);
                return;
            }
        }
    }

    /**
     * Normalize a token for safe CSS class usage.
     *
     * @param {string} value Raw token string to normalize.
     * @returns {string} Lowercase alphanumeric string with hyphens and underscores only.
     */
    function normalizeToken(value) {
        return String(value || '').toLowerCase().replace(/[^a-z0-9_-]/g, '');
    }

    /**
     * Get localized labels for pending items.
     *
     * @param {Object} config Widget configuration.
     * @returns {Object} Object with available, locked and open label strings.
     */
    function getPendingLabels(config) {
        return {
            available: getConfigString(config, 'pendingstatusavailable'),
            locked: getConfigString(config, 'pendingstatuslocked'),
            open: getConfigString(config, 'pendingopenactivity')
        };
    }

    /**
     * Get the normalized percentage value.
     *
     * @param {Object} config Widget configuration.
     * @returns {number} Value clamped between 0 and 100.
     */
    function getNormalizedValue(config) {
        var rawvalue = config && Number.isFinite(Number(config.value)) ? Number(config.value) : 0;

        return Math.max(0, Math.min(100, rawvalue));
    }

    /**
     * Build the pending timeline items collection.
     *
     * @param {Object} config Widget configuration.
     * @param {Object} labels Localized labels.
     * @returns {Array<Object>} Array of pending item context objects.
     */
    function buildPendingItems(config, labels) {
        var items = config && Array.isArray(config.pendingitems) ? config.pendingitems : [];

        return items.map(function(item) {
            return buildPendingItemContext(item, labels);
        });
    }

    /**
     * Resolve the display detail text for a pending item.
     *
     * @param {Object} item Raw pending item from config.
     * @returns {string} The detail string to display.
     */
    function resolvePendingItemDetail(item) {
        if (item && item.detail) {
            return item.detail;
        }
        if (item && item.name) {
            return item.name;
        }
        return '';
    }

    /**
     * Build the template context for one pending timeline item.
     *
     * @param {Object} item Raw pending item data.
     * @param {Object} labels Localized label strings.
     * @returns {Object} Mustache context object for a single pending item.
     */
    function buildPendingItemContext(item, labels) {
        var available = Number(item && item.available) === 1;
        var url = item && item.url ? item.url : '';
        var statusClass = available ? 'is-available' : 'is-locked';
        var statusLabel = available ? labels.available : labels.locked;

        return {
            name: item && item.name ? item.name : '',
            detail: resolvePendingItemDetail(item),
            type: item && item.type ? item.type : '',
            modname: normalizeToken(item && item.modname ? item.modname : ''),
            status: statusLabel,
            statusclass: statusClass,
            url: url,
            hasurl: !!url,
            linklabel: item && item.linklabel ? item.linklabel : labels.open,
            availabilityinfo: item && item.availabilityinfo ? item.availabilityinfo : ''
        };
    }

    /**
     * Build the progress bar context fields.
     *
     * @param {Object} config Widget configuration.
     * @param {number} value Normalized percentage value.
     * @returns {Object} Partial Mustache context for the progress bar.
     */
    function buildProgressContext(config, value) {
        return {
            label: getConfigString(config, 'label'),
            value: value,
            percentage: value + '%',
            maxlabel: getConfigString(config, 'maxlabel') || '100%',
            showpercentage: getConfigFlag(config, 'showpercentage', true)
        };
    }

    /**
     * Build the modal and pending-items context fields.
     *
     * @param {Object} config Widget configuration.
     * @param {Array<Object>} pendingitems Processed pending item context array.
     * @returns {Object} Partial Mustache context for the modal section.
     */
    function buildModalContext(config, pendingitems) {
        return {
            showpendingbutton: getConfigFlag(config, 'showpendingbutton', false),
            pendingbuttonlabel: getConfigString(config, 'pendingbuttonlabel'),
            pendingtitle: getConfigString(config, 'pendingtitle'),
            closemodal: getConfigString(config, 'closemodal'),
            pendingempty: getConfigString(config, 'pendingempty'),
            haspendingitems: pendingitems.length > 0,
            pendingitems: pendingitems
        };
    }

    /**
     * Build the full Mustache context for the widget.
     *
     * @param {Object} config Raw widget configuration object.
     * @returns {Object} Complete Mustache context ready for template rendering.
     */
    function buildContext(config) {
        var labels = getPendingLabels(config);
        var value = getNormalizedValue(config);
        var pendingitems = buildPendingItems(config, labels);
        var progressCtx = buildProgressContext(config, value);
        var modalCtx = buildModalContext(config, pendingitems);

        return Object.assign({}, progressCtx, modalCtx);
    }

    /**
     * Hide the pending-items modal.
     *
     * @param {HTMLElement} container The widget root element.
     * @returns {void}
     */
    function closeModal(container) {
        var modal = container.querySelector('.local-courseprogresspro__modal');
        if (!modal) {
            return;
        }

        modal.hidden = true;
        document.body.classList.remove('local-courseprogresspro-modal-open');
    }

    /**
     * Show the pending-items modal.
     *
     * @param {HTMLElement} container The widget root element.
     * @returns {void}
     */
    function openModal(container) {
        var modal = container.querySelector('.local-courseprogresspro__modal');
        if (!modal) {
            return;
        }

        modal.hidden = false;
        document.body.classList.add('local-courseprogresspro-modal-open');
    }

    /**
     * Bind modal open and close interactions once.
     *
     * @param {HTMLElement} container The widget root element.
     * @returns {void}
     */
    function bindModal(container) {
        if (container.dataset.bound === '1') {
            return;
        }

        container.dataset.bound = '1';
        container.addEventListener('click', function(event) {
            var action = event.target.closest('[data-action]');
            if (!action) {
                return;
            }

            var currentAction = action.getAttribute('data-action');
            if (currentAction === 'open-pending') {
                openModal(container);
            }

            if (currentAction === 'close-pending') {
                closeModal(container);
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal(container);
            }
        });
    }

    /**
     * Initialize and render the progress widget.
     *
     * @param {Object} config Raw widget configuration object.
     * @returns {(Promise<HTMLElement>|undefined)} Promise resolving to the container element, or undefined if already initialized.
     */
    function init(config) {
        var container = getOrCreateContainer();
        if (container.dataset.initialized === '1') {
            return undefined;
        }

        container.dataset.initialized = '1';
        moveToCourseContent(container);

        return Templates.renderForPromise('local_courseprogresspro/progress_widget', buildContext(config))
            .then(function(rendered) {
                return Templates.replaceNodeContents(container, rendered.html, rendered.js);
            })
            .then(function() {
                bindModal(container);
                return container;
            })
            .catch(function(error) {
                container.dataset.initialized = '';
                throw error;
            });
    }

    return {
        init: init
    };
});
