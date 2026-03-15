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

    function normalizeToken(value) {
        return String(value || '').toLowerCase().replace(/[^a-z0-9_-]/g, '');
    }

    function buildPendingItemContext(item, labels) {
        var available = Number(item && item.available) === 1;
        var url = item && item.url ? item.url : '';

        return {
            name: item && item.name ? item.name : '',
            detail: item && item.detail ? item.detail : (item && item.name ? item.name : ''),
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

    function buildContext(config) {
        var value = config && Number.isFinite(Number(config.value)) ? Number(config.value) : 0;
        var labels = {
            available: config && config.pendingstatusavailable ? config.pendingstatusavailable : 'Disponible ahora',
            locked: config && config.pendingstatuslocked ? config.pendingstatuslocked : 'Aun no disponible',
            open: config && config.pendingopenactivity ? config.pendingopenactivity : 'Abrir actividad'
        };

        value = Math.max(0, Math.min(100, value));

        var pendingitems = config && Array.isArray(config.pendingitems) ?
            config.pendingitems.map(function(item) {
                return buildPendingItemContext(item, labels);
            }) : [];

        return {
            label: config && config.label ? config.label : 'Barra de progreso',
            value: value,
            percentage: value + '%',
            maxlabel: config && config.maxlabel ? config.maxlabel : '100%',
            showpercentage: !config || Number(config.showpercentage) === 1,
            showpendingbutton: config && Number(config.showpendingbutton) === 1,
            pendingbuttonlabel: config && config.pendingbuttonlabel ? config.pendingbuttonlabel : 'Ver acciones pendientes',
            pendingtitle: config && config.pendingtitle ? config.pendingtitle : 'Acciones pendientes',
            closemodal: config && config.closemodal ? config.closemodal : 'Cerrar ventana',
            pendingempty: config && config.pendingempty ? config.pendingempty : 'No hay actividades pendientes.',
            haspendingitems: pendingitems.length > 0,
            pendingitems: pendingitems
        };
    }

    function closeModal(container) {
        var modal = container.querySelector('.local-courseprogresspro__modal');
        if (!modal) {
            return;
        }

        modal.hidden = true;
        document.body.classList.remove('local-courseprogresspro-modal-open');
    }

    function openModal(container) {
        var modal = container.querySelector('.local-courseprogresspro__modal');
        if (!modal) {
            return;
        }

        modal.hidden = false;
        document.body.classList.add('local-courseprogresspro-modal-open');
    }

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

    function init(config) {
        var container = getOrCreateContainer();
        if (container.dataset.initialized === '1') {
            return;
        }

        container.dataset.initialized = '1';
        moveToCourseContent(container);

        Templates.renderForPromise('local_courseprogresspro/progress_widget', buildContext(config))
            .then(function(rendered) {
                return Templates.replaceNodeContents(container, rendered.html, rendered.js);
            })
            .then(function() {
                bindModal(container);
            })
            .catch(function() {
                container.dataset.initialized = '';
            });
    }

    return {
        init: init
    };
});
