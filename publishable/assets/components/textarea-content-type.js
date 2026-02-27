(function (window, document, $) {
    if (!$) {
        return;
    }

    window.mfwInputableTinymceRuntime = window.mfwInputableTinymceRuntime || {
        simplifiedSettingsPromise: null,
    };

    function mfwInputableTinymceBaseHref() {
        const origin = window.location.origin ?? (window.location.protocol + '//' + window.location.host);

        return origin.replace(/\/$/, '') + '/';
    }

    function mfwInputableSimplifiedSettingsUrl() {
        return mfwInputableTinymceBaseHref() + 'vendor/mfw-inputable/js/tinymce/simplified.js';
    }

    function mfwInputableEnsureSimplifiedTinymceSettings() {
        if (typeof window.mfw_simplified_tinymce_settings === 'function') {
            return $.Deferred().resolve().promise();
        }

        if (window.mfwInputableTinymceRuntime.simplifiedSettingsPromise) {
            return window.mfwInputableTinymceRuntime.simplifiedSettingsPromise;
        }

        const url = mfwInputableSimplifiedSettingsUrl();
        window.mfwInputableTinymceRuntime.simplifiedSettingsPromise = $.getScript(url);

        return window.mfwInputableTinymceRuntime.simplifiedSettingsPromise;
    }

    function mfwInputableInitTinyMce(textarea) {
        if (!window.tinymce || !textarea || !textarea.id) {
            return;
        }

        if (window.tinymce.get(textarea.id)) {
            return;
        }

        const preset = (textarea.getAttribute('data-mfw-inputable-tinymce-preset') || 'simplified').toLowerCase();
        const selector = '#' + textarea.id;

        if (preset === 'extended') {
            if (typeof window.mfw_default_tinymce_settings === 'function') {
                window.tinymce.init(window.mfw_default_tinymce_settings(selector));
            }

            return;
        }

        $.when(mfwInputableEnsureSimplifiedTinymceSettings()).then(function () {
            if (typeof window.mfw_simplified_tinymce_settings === 'function') {
                window.tinymce.init(window.mfw_simplified_tinymce_settings(selector));
            }
        });
    }

    function mfwInputableInitModeTextareas() {
        $('[data-mfw-inputable-editor-mode="html"]').each(function () {
            mfwInputableInitTinyMce(this);
        });
    }

    window.mfwInputableInitTinyMceTextareas = mfwInputableInitModeTextareas;

    $(function () {
        mfwInputableInitModeTextareas();
    });
})(window, document, window.jQuery);
