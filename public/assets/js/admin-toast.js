/**
 * Thông báo admin (Notyf).
 * Gọi: showAdminToast(message, type, options?)
 * type: primary | secondary | success | danger | error | warning | info | dark
 */
(function () {
    if (window.__adminToastInit) return;
    window.__adminToastInit = true;

    var TYPE_DEFAULTS = {
        primary: { title: 'Thông báo' },
        secondary: { title: 'Thông báo' },
        success: { title: 'Thành công' },
        error: { title: 'Lỗi' },
        warning: { title: 'Cảnh báo' },
        info: { title: 'Thông tin' },
        dark: { title: 'Thông báo' }
    };

    var notyf = null;

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function getColors() {
        return (window.config && window.config.colors) ? window.config.colors : {};
    }

    function normalizeType(type) {
        var key = (type || 'info').toLowerCase();
        if (key === 'danger') {
            key = 'error';
        }
        return TYPE_DEFAULTS[key] ? key : 'info';
    }

    function buildMessage(message, title) {
        var msg = message || '';
        var ttl = title || '';
        if (ttl && msg) {
            return '<div class="notyf__title">' + escapeHtml(ttl) + '</div>' +
                '<div class="notyf__text">' + escapeHtml(msg) + '</div>';
        }
        return escapeHtml(ttl || msg);
    }

    function initNotyf() {
        if (notyf || typeof Notyf === 'undefined') {
            return notyf;
        }

        var colors = getColors();

        class CustomNotyf extends Notyf {
            _renderNotification(options) {
                const notification = super._renderNotification(options);
                if (options.message) {
                    notification.message.innerHTML = options.message;
                }
                return notification;
            }
        }

        notyf = new CustomNotyf({
            duration: 3000,
            ripple: true,
            dismissible: false,
            position: { x: 'right', y: 'top' },
            types: [
                {
                    type: 'success',
                    background: colors.success,
                    className: 'notyf__success',
                    icon: {
                        className: 'icon-base ti tabler-circle-check-filled icon-md text-white',
                        tagName: 'i'
                    }
                },
                {
                    type: 'error',
                    background: colors.danger,
                    className: 'notyf__error',
                    icon: {
                        className: 'icon-base ti tabler-xbox-x-filled icon-md text-white',
                        tagName: 'i'
                    }
                },
                {
                    type: 'warning',
                    background: colors.warning,
                    className: 'notyf__warning',
                    icon: {
                        className: 'icon-base ti tabler-alert-triangle-filled icon-md text-white',
                        tagName: 'i'
                    }
                },
                {
                    type: 'info',
                    background: colors.info,
                    className: 'notyf__info',
                    icon: {
                        className: 'icon-base ti tabler-info-circle-filled icon-md text-white',
                        tagName: 'i'
                    }
                },
                {
                    type: 'primary',
                    background: colors.primary,
                    className: 'notyf__primary',
                    icon: {
                        className: 'icon-base ti tabler-bell-filled icon-md text-white',
                        tagName: 'i'
                    }
                },
                {
                    type: 'secondary',
                    background: colors.secondary,
                    className: 'notyf__secondary',
                    icon: {
                        className: 'icon-base ti tabler-bell-filled icon-md text-white',
                        tagName: 'i'
                    }
                },
                {
                    type: 'dark',
                    background: colors.dark,
                    className: 'notyf__dark',
                    icon: {
                        className: 'icon-base ti tabler-bell-filled icon-md text-white',
                        tagName: 'i'
                    }
                }
            ]
        });

        return notyf;
    }

    window.showAdminToast = function (message, type, options) {
        options = options || {};
        var instance = initNotyf();
        if (!instance) {
            window.alert(message || '');
            return;
        }

        var normalizedType = normalizeType(type);
        var defaults = TYPE_DEFAULTS[normalizedType];
        var title = options.title != null ? options.title : defaults.title;
        var delay = options.delay != null ? options.delay : 3000;

        instance.open({
            type: normalizedType,
            message: buildMessage(message, title),
            duration: delay,
            dismissible: !!options.dismissible,
            ripple: options.ripple !== false,
            position: options.position || { x: 'right', y: 'top' }
        });
    };

    window.dismissAdminToast = function () {
        if (notyf) {
            notyf.dismissAll();
        }
    };
})();
