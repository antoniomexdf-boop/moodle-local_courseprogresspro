define([], function() {
    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

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

    function renderPendingItems(items, emptyText, labels) {
        if (!items || !items.length) {
            return '<p class="local-courseprogresspro__empty">' + escapeHtml(emptyText) + '</p>';
        }

        var html = items.map(function(item) {
            if (typeof item === 'string') {
                return '<li class="local-courseprogresspro__pending-item">' + escapeHtml(item) + '</li>';
            }

            var name = item && item.name ? item.name : '';
            var detail = item && item.detail ? item.detail : name;
            var type = item && item.type ? item.type : '';
            var modname = normalizeToken(item && item.modname ? item.modname : '');
            var available = Number(item && item.available) === 1;
            var url = item && item.url ? item.url : '';
            var status = available ? labels.available : labels.locked;
            var statusClass = available ? 'is-available' : 'is-locked';
            var actionHtml = '';

            if (available && url) {
                actionHtml = '<a class="local-courseprogresspro__pending-link" href="' + escapeHtml(url) + '">' +
                    escapeHtml(labels.open) + '</a>';
            }

            return '' +
                '<li class="local-courseprogresspro__pending-item local-courseprogresspro__pending-item--timeline ' + statusClass + '">' +
                    '<span class="local-courseprogresspro__dot" aria-hidden="true"></span>' +
                    '<div class="local-courseprogresspro__pending-content">' +
                        '<div class="local-courseprogresspro__pending-top">' +
                            '<span class="local-courseprogresspro__pending-name">' + escapeHtml(name) + '</span>' +
                            '<span class="local-courseprogresspro__pending-type local-courseprogresspro__pending-type--' + modname + '">' +
                                escapeHtml(type) + '</span>' +
                        '</div>' +
                        '<div class="local-courseprogresspro__pending-detail">' + escapeHtml(detail) + '</div>' +
                        '<div class="local-courseprogresspro__pending-meta">' +
                            '<span class="local-courseprogresspro__pending-status ' + statusClass + '">' + escapeHtml(status) + '</span>' +
                            actionHtml +
                        '</div>' +
                    '</div>' +
                '</li>';
        });

        return '<ul class="local-courseprogresspro__pending-list">' + html.join('') + '</ul>';
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

        var label = config && config.label ? config.label : 'Barra de progreso';
        var completedcount = config && config.completedcount ? config.completedcount : '';
        var value = config && Number.isFinite(Number(config.value)) ? Number(config.value) : 0;
        var maxlabel = config && config.maxlabel ? config.maxlabel : '100%';
        var showpercentage = !config || Number(config.showpercentage) === 1;
        var showcompletedcount = config && Number(config.showcompletedcount) === 1;
        var showpendingbutton = config && Number(config.showpendingbutton) === 1;
        var pendingbuttonlabel = config && config.pendingbuttonlabel ? config.pendingbuttonlabel : 'Ver acciones pendientes';
        var pendingtitle = config && config.pendingtitle ? config.pendingtitle : 'Acciones pendientes';
        var pendingempty = config && config.pendingempty ? config.pendingempty : 'No hay actividades pendientes.';
        var closemodal = config && config.closemodal ? config.closemodal : 'Cerrar ventana';
        var pendingstatusavailable = config && config.pendingstatusavailable ? config.pendingstatusavailable : 'Disponible ahora';
        var pendingstatuslocked = config && config.pendingstatuslocked ? config.pendingstatuslocked : 'Aun no disponible';
        var pendingopenactivity = config && config.pendingopenactivity ? config.pendingopenactivity : 'Abrir actividad';
        var pendingitems = config && Array.isArray(config.pendingitems) ? config.pendingitems : [];
        value = Math.max(0, Math.min(100, value));

        container.innerHTML =
            '<div class="local-courseprogresspro__title">' + label + '</div>' +
            '<div class="local-courseprogresspro__track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' + value + '">' +
                '<span class="local-courseprogresspro__fill" style="width: ' + value + '%;"></span>' +
            '</div>' +
            '<div class="local-courseprogresspro__meta">' +
                '<div class="local-courseprogresspro__value">' + (showpercentage ? value + '%' : '') + '</div>' +
                '<div class="local-courseprogresspro__max">' + maxlabel + '</div>' +
            '</div>' +
            '<div class="local-courseprogresspro__count">' + (showcompletedcount ? completedcount : '') + '</div>' +
            '<div class="local-courseprogresspro__actions">' +
                (showpendingbutton ? '<button type="button" class="local-courseprogresspro__button" data-action="open-pending">' +
                    escapeHtml(pendingbuttonlabel) + '</button>' : '') +
            '</div>' +
            '<div class="local-courseprogresspro__modal" hidden="hidden" role="dialog" aria-modal="true">' +
                '<button type="button" class="local-courseprogresspro__backdrop" data-action="close-pending" aria-label="' + escapeHtml(closemodal) + '"></button>' +
                '<div class="local-courseprogresspro__modal-card">' +
                    '<div class="local-courseprogresspro__modal-header">' +
                        '<div class="local-courseprogresspro__modal-title">' + escapeHtml(pendingtitle) + '</div>' +
                        '<button type="button" class="local-courseprogresspro__close" data-action="close-pending" aria-label="' + escapeHtml(closemodal) + '">&times;</button>' +
                    '</div>' +
                    renderPendingItems(pendingitems, pendingempty, {
                        available: pendingstatusavailable,
                        locked: pendingstatuslocked,
                        open: pendingopenactivity
                    }) +
                '</div>' +
            '</div>';

        bindModal(container);
    }

    return {
        init: init
    };
});
