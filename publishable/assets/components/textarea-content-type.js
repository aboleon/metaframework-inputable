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

    function mfwInputableInitRegularTinymce() {
        if (!window.tinymce || typeof window.mfw_default_tinymce_settings !== 'function') {
            return;
        }

        const extendedSelector = 'textarea.extended:not([data-mfw-inputable-content-type-enabled])';
        const simplifiedSelector = 'textarea.simplified:not([data-mfw-inputable-content-type-enabled])';

        if ($(extendedSelector).length) {
            window.tinymce.init(window.mfw_default_tinymce_settings(extendedSelector));
        }

        if ($(simplifiedSelector).length) {
            $.when(mfwInputableEnsureSimplifiedTinymceSettings()).then(function () {
                window.tinymce.init(window.mfw_simplified_tinymce_settings(simplifiedSelector));
            });
        }
    }

    function mfwInputableHtmlToTextareaText(html) {
        const container = document.createElement('div');
        container.innerHTML = html || '';

        Array.prototype.slice.call(container.querySelectorAll('br')).forEach(function (node) {
            if (!node.parentNode) {
                return;
            }

            node.parentNode.replaceChild(document.createTextNode('\n'), node);
        });

        const blockSelector = 'p,div,li,tr,table,thead,tbody,tfoot,section,article,blockquote,h1,h2,h3,h4,h5,h6,ul,ol';
        Array.prototype.slice.call(container.querySelectorAll(blockSelector)).forEach(function (node) {
            node.appendChild(document.createTextNode('\n'));
        });

        return (container.textContent || '')
            .replace(/\u00a0/g, ' ')
            .replace(/\r\n/g, '\n')
            .replace(/\n{3,}/g, '\n\n');
    }

    function mfwInputableRemoveTinyMce(textarea) {
        if (!window.tinymce || !textarea || !textarea.id) {
            return;
        }

        const editor = window.tinymce.get(textarea.id);

        if (!editor) {
            return;
        }

        const html = editor.getContent({ format: 'html' });
        editor.remove();
        textarea.value = mfwInputableHtmlToTextareaText(html);
        $(textarea).trigger('input').trigger('change');
    }

    function mfwInputableInitTinyMce(textarea) {
        if (!window.tinymce || !textarea || !textarea.id) {
            return;
        }

        if (window.tinymce.get(textarea.id)) {
            return;
        }

        const preset = textarea.getAttribute('data-mfw-inputable-tinymce-preset') || 'simplified';
        const selector = '#' + textarea.id;

        if (preset === 'extended') {
            if (typeof window.mfw_default_tinymce_settings === 'function') {
                window.tinymce.init(window.mfw_default_tinymce_settings(selector));
            }

            return;
        }

        $.when(mfwInputableEnsureSimplifiedTinymceSettings()).then(function () {
            window.tinymce.init(window.mfw_simplified_tinymce_settings(selector));
        });
    }

    function mfwInputableApplyContentType(textarea, hiddenInput, type) {
        if (!textarea || !hiddenInput) {
            return;
        }

        hiddenInput.value = type;

        if (type === 'html') {
            mfwInputableInitTinyMce(textarea);

            return;
        }

        mfwInputableRemoveTinyMce(textarea);
    }

    function mfwInputableContentTypeElements(targetId) {
        return {
            textarea: document.getElementById(targetId),
            hiddenInput: document.querySelector('input[data-mfw-inputable-content-type-hidden="' + targetId + '"]'),
            checkedRadio: document.querySelector('input[data-mfw-inputable-content-type-radio="' + targetId + '"]:checked'),
        };
    }

    function mfwInputableInitContentTypeTextareas() {
        $('[data-mfw-inputable-content-type-enabled]').each(function () {
            const targetId = this.id;
            const elements = mfwInputableContentTypeElements(targetId);
            const selectedValue = elements.checkedRadio
                ? elements.checkedRadio.value
                : (elements.hiddenInput ? elements.hiddenInput.value : 'text');

            mfwInputableApplyContentType(elements.textarea, elements.hiddenInput, selectedValue);
        });
    }

    $(function () {
        mfwInputableInitRegularTinymce();
        mfwInputableInitContentTypeTextareas();

        $(document)
            .off('change.mfwInputableContentType', 'input[data-mfw-inputable-content-type-radio]')
            .on('change.mfwInputableContentType', 'input[data-mfw-inputable-content-type-radio]', function () {
                const targetId = this.getAttribute('data-mfw-inputable-content-type-radio');
                const elements = mfwInputableContentTypeElements(targetId);

                mfwInputableApplyContentType(elements.textarea, elements.hiddenInput, this.value);
            });
    });
})(window, document, window.jQuery);
