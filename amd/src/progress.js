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
     * Returns a string configuration value or an empty fallback.
     *
     * @param {Object} config Widget configuration.
     * @param {string} key Configuration key.
     * @return {string}
     */
    function getConfigString(config, key) {
        return config && config[key] ? config[key] : '';
    }

    /**
     * Returns whether a numeric config flag is enabled.
     *
     * @param {Object} config Widget configuration.
     * @param {string} key Configuration key.
     * @param {boolean} fallback Default value.
     * @return {boolean}
     */
    function getConfigFlag(config, key, fallback) {
        if (!config || typeof config[key] === 'undefined') {
            return fallback;
        }

        return Number(config[key]) === 1;
    }

    /**
     * Creates or reuses the root widget container.
     *
     * @return {HTMLElement}
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
     * Moves the widget container into the main course content area.
     *
     * @param {HTMLElement} container
     * @return {void}
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
     * Normalizes a token for safe CSS class usage.
     *
     * @param {string} value
     * @return {string}
     */
    function normalizeToken(value) {
        return String(value || '').toLowerCase().replace(/[^a-z0-9_-]/g, '');
    }

    /**
     * Builds the template context for one pending timeline item.
     *
     * @param {Object} item
     * @param {Object} labels
     * @return {Object}
     */
    function buildPendingItemContext(item, labels) {
        var available = Number(item && item.available) === 1;
        var url = item && item.url ? item.url : '';
        var detail = '';

        if (item && item.detail) {
            detail = item.detail;
        } else if (item && item.name) {
            detail = item.name;
        }

        return {
            name: item && item.name ? item.name : '',
            detail: detail,
            type: item && item.type ? item.type : '',
            modname: normalizeToken(item && item.modname ? item.modname : ''),
            status: available ? labels.available : labels.locked,
            statusclass: available ? 'is-available' : 'is-locked',
            url: url,
            hasurl: !!url,
            linklabel: item && item.linklabel ? item.linklabel : labels.open,
            availabilityinfo: item && item.availabilityinfo ? item.availabilityinfo : ''
        };
    }

    /**
     * Builds the full Mustache context for the widget.
     *
     * @param {Object} config
     * @return {Object}
     */
    function buildContext(config) {
        var rawvalue = config && Number.isFinite(Number(config.value)) ? Number(config.value) : 0;
        var labels = {
            available: getConfigString(config, 'pendingstatusavailable'),
            locked: getConfigString(config, 'pendingstatuslocked'),
            open: getConfigString(config, 'pendingopenactivity')
        };
        var value = Math.max(0, Math.min(100, rawvalue));
        var items = config && Array.isArray(config.pendingitems) ? config.pendingitems : [];
        var pendingitems = items.map(function(item) {
            return buildPendingItemContext(item, labels);
        });

        return {
            label: getConfigString(config, 'label'),
            value: value,
            percentage: value + '%',
            maxlabel: getConfigString(config, 'maxlabel') || '100%',
            showpercentage: getConfigFlag(config, 'showpercentage', true),
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
     * Hides the pending-items modal.
     *
     * @param {HTMLElement} container
     * @return {void}
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
     * Shows the pending-items modal.
     *
     * @param {HTMLElement} container
     * @return {void}
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
     * Binds modal open and close interactions once.
     *
     * @param {HTMLElement} container
     * @return {void}
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
     * Initializes and renders the progress widget.
     *
     * @param {Object} config
     * @return {Promise|undefined}
     */
    function init(config) {
        var container = getOrCreateContainer();
        if (container.dataset.initialized === '1') {
            return;
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
