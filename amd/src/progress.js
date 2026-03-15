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
     * @returns {string}
     */
    function getConfigString(config, key) {
        return config && config[key] ? config[key] : '';
    }

    /**
     * Get whether a numeric config flag is enabled.
     *
     * @param {Object} config Widget configuration.
     * @param {string} key Configuration key.
     * @param {boolean} fallback Default value.
     * @returns {boolean}
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
     * @returns {HTMLElement}
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
     * @param {HTMLElement} container Widget container.
     * @returns {void}
     */
    function moveToCourseContent(container) {
        var selectors = [
            '#region-main',
            '#page-content .container-fluid',
            '#page-content',
            '.course-content'
        ];
        var i;
        var target = null;

        for (i = 0; i < selectors.length; i++) {
            target = document.querySelector(selectors[i]);

            if (target) {
                target.insertBefore(container, target.firstChild);
                return;
            }
        }
    }

    /**
     * Normalize a token for safe CSS class usage.
     *
     * @param {string} value Raw token value.
     * @returns {string}
     */
    function normalizeToken(value) {
        return String(value || '').toLowerCase().replace(/[^a-z0-9_-]/g, '');
    }

    /**
     * Get localized labels for pending items.
     *
     * @param {Object} config Widget configuration.
     * @returns {Object}
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
     * @returns {number}
     */
    function getNormalizedValue(config) {
        var rawvalue = 0;

        if (config && Number.isFinite(Number(config.value))) {
            rawvalue = Number(config.value);
        }

        return Math.max(0, Math.min(100, rawvalue));
    }

    /**
     * Get the pending item detail text.
     *
     * @param {Object} item Pending item data.
     * @returns {string}
     */
    function getPendingItemDetail(item) {
        if (item && item.detail) {
            return item.detail;
        }

        if (item && item.name) {
            return item.name;
        }

        return '';
    }

    /**
     * Get the pending item status label.
     *
     * @param {boolean} available Whether the item is available.
     * @param {Object} labels Localized labels.
     * @returns {string}
     */
    function getPendingStatusLabel(available, labels) {
        if (available) {
            return labels.available;
        }

        return labels.locked;
    }

    /**
     * Get the pending item status CSS class.
     *
     * @param {boolean} available Whether the item is available.
     * @returns {string}
     */
    function getPendingStatusClass(available) {
        if (available) {
            return 'is-available';
        }

        return 'is-locked';
    }

    /**
     * Build the template context for one pending timeline item.
     *
     * @param {Object} item Pending item data.
     * @param {Object} labels Localized labels.
     * @returns {Object}
     */
    function buildPendingItemContext(item, labels) {
        var available = Number(item && item.available) === 1;
        var url = item && item.url ? item.url : '';

        return {
            name: item && item.name ? item.name : '',
            detail: getPendingItemDetail(item),
            type: item && item.type ? item.type : '',
            modname: normalizeToken(item && item.modname ? item.modname : ''),
            status: getPendingStatusLabel(available, labels),
            statusclass: getPendingStatusClass(available),
            url: url,
            hasurl: !!url,
            linklabel: item && item.linklabel ? item.linklabel : labels.open,
            availabilityinfo: item && item.availabilityinfo ? item.availabilityinfo : ''
        };
    }

    /**
     * Map one pending item into template context.
     *
     * @param {Object} item Pending item data.
     * @param {Object} labels Localized labels.
     * @returns {Object}
     */
    function mapPendingItem(item, labels) {
        return buildPendingItemContext(item, labels);
    }

    /**
     * Build the pending timeline items collection.
     *
     * @param {Object} config Widget configuration.
     * @param {Object} labels Localized labels.
     * @returns {Array}
     */
    function buildPendingItems(config, labels) {
        var items = config && Array.isArray(config.pendingitems) ? config.pendingitems : [];

        return items.map(function(item) {
            return mapPendingItem(item, labels);
        });
    }

    /**
     * Build the static widget labels.
     *
     * @param {Object} config Widget configuration.
     * @returns {Object}
     */
    function buildWidgetLabels(config) {
        return {
            label: getConfigString(config, 'label'),
            maxlabel: getConfigString(config, 'maxlabel') || '100%',
            pendingbuttonlabel: getConfigString(config, 'pendingbuttonlabel'),
            pendingtitle: getConfigString(config, 'pendingtitle'),
            closemodal: getConfigString(config, 'closemodal'),
            pendingempty: getConfigString(config, 'pendingempty')
        };
    }

    /**
     * Build widget display flags.
     *
     * @param {Object} config Widget configuration.
     * @param {Array} pendingitems Pending items.
     * @returns {Object}
     */
    function buildWidgetFlags(config, pendingitems) {
        return {
            showpercentage: getConfigFlag(config, 'showpercentage', true),
            showpendingbutton: getConfigFlag(config, 'showpendingbutton', false),
            haspendingitems: pendingitems.length > 0
        };
    }

    /**
     * Build the full Mustache context for the widget.
     *
     * @param {Object} config Widget configuration.
     * @returns {Object}
     */
    function buildContext(config) {
        var labels = getPendingLabels(config);
        var value = getNormalizedValue(config);
        var pendingitems = buildPendingItems(config, labels);
        var widgetlabels = buildWidgetLabels(config);
        var widgetflags = buildWidgetFlags(config, pendingitems);

        return {
            label: widgetlabels.label,
            value: value,
            percentage: value + '%',
            maxlabel: widgetlabels.maxlabel,
            showpercentage: widgetflags.showpercentage,
            showpendingbutton: widgetflags.showpendingbutton,
            pendingbuttonlabel: widgetlabels.pendingbuttonlabel,
            pendingtitle: widgetlabels.pendingtitle,
            closemodal: widgetlabels.closemodal,
            pendingempty: widgetlabels.pendingempty,
            haspendingitems: widgetflags.haspendingitems,
            pendingitems: pendingitems
        };
    }

    /**
     * Hide the pending-items modal.
     *
     * @param {HTMLElement} container Widget container.
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
     * @param {HTMLElement} container Widget container.
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
     * Handle click interactions in the widget container.
     *
     * @param {HTMLElement} container Widget container.
     * @param {Event} event Browser click event.
     * @returns {void}
     */
    function handleContainerClick(container, event) {
        var action = event.target.closest('[data-action]');
        var currentaction = null;

        if (!action) {
            return;
        }

        currentaction = action.getAttribute('data-action');

        if (currentaction === 'open-pending') {
            openModal(container);
            return;
        }

        if (currentaction === 'close-pending') {
            closeModal(container);
        }
    }

    /**
     * Handle global keydown interactions for the widget.
     *
     * @param {HTMLElement} container Widget container.
     * @param {KeyboardEvent} event Keyboard event.
     * @returns {void}
     */
    function handleDocumentKeydown(container, event) {
        if (event.key === 'Escape') {
            closeModal(container);
        }
    }

    /**
     * Bind modal open and close interactions once.
     *
     * @param {HTMLElement} container Widget container.
     * @returns {void}
     */
    function bindModal(container) {
        if (container.dataset.bound === '1') {
            return;
        }

        container.dataset.bound = '1';

        container.addEventListener('click', function(event) {
            handleContainerClick(container, event);
        });

        document.addEventListener('keydown', function(event) {
            handleDocumentKeydown(container, event);
        });
    }

    /**
     * Render the widget template into the container.
     *
     * @param {HTMLElement} container Widget container.
     * @param {Object} config Widget configuration.
     * @returns {Promise}
     */
    function renderWidget(container, config) {
        return Templates.renderForPromise('local_courseprogresspro/progress_widget', buildContext(config))
            .then(function(rendered) {
                return Templates.replaceNodeContents(container, rendered.html, rendered.js);
            })
            .then(function() {
                bindModal(container);
                return container;
            });
    }

    /**
     * Handle rendering errors and reset initialization state.
     *
     * @param {HTMLElement} container Widget container.
     * @param {*} error Rendering error.
     * @returns {Promise}
     */
    function handleRenderError(container, error) {
        container.dataset.initialized = '';
        return Promise.reject(error);
    }

    /**
     * Initialize and render the progress widget.
     *
     * @param {Object} config Widget configuration.
     * @returns {Promise|undefined}
     */
    function init(config) {
        var container = getOrCreateContainer();

        if (container.dataset.initialized === '1') {
            return;
        }

        container.dataset.initialized = '1';
        moveToCourseContent(container);

        return renderWidget(container, config)
            .catch(function(error) {
                return handleRenderError(container, error);
            });
    }

    return {
        init: init
    };
});