(function (window, document) {
    if (!window || !document) {
        return;
    }

    let registry = window.mfwInputableCherryEditors || {};
    let submitListenerBound = window.mfwInputableCherrySubmitListenerBound === true;

    window.mfwInputableCherryEditors = registry;

    function cherryConstructor() {
        return window.Cherry;
    }

    function cherryLocaleRegistry() {
        window.mfwInputableCherryLocaleOverrides = window.mfwInputableCherryLocaleOverrides || {};

        return window.mfwInputableCherryLocaleOverrides;
    }

    function cherryLocalePromises() {
        window.mfwInputableCherryLocalePromises = window.mfwInputableCherryLocalePromises || {};

        return window.mfwInputableCherryLocalePromises;
    }

    function resolveCherryLocale(rawLocale) {
        let normalized = String(rawLocale || '').trim().replace('-', '_').toLowerCase();

        if (normalized.startsWith('fr')) {
            return 'fr_FR';
        }

        if (normalized.startsWith('bg')) {
            return 'bg_BG';
        }

        if (normalized.startsWith('ru')) {
            return 'ru_RU';
        }

        if (normalized.startsWith('zh')) {
            return 'zh_CN';
        }

        return 'en_US';
    }

    function localeNeedsAsyncPack(locale) {
        return locale === 'fr_FR' || locale === 'bg_BG';
    }

    function runtimeAssetBase() {
        let currentScript = document.currentScript;
        let origin = window.location.origin || (window.location.protocol + '//' + window.location.host);

        if (currentScript && currentScript.src) {
            return currentScript.src.replace(/textarea-cherry\.js(?:\?.*)?$/, '');
        }

        return origin.replace(/\/$/, '') + '/vendor/mfw-inputable/components/';
    }

    function ensureLocalePack(locale) {
        let localeRegistry = cherryLocaleRegistry();
        let promises = cherryLocalePromises();
        let source;

        if (!localeNeedsAsyncPack(locale)) {
            return Promise.resolve(null);
        }

        if (localeRegistry[locale] && typeof localeRegistry[locale] === 'object') {
            return Promise.resolve(localeRegistry[locale]);
        }

        if (promises[locale]) {
            return promises[locale];
        }

        source = runtimeAssetBase() + 'cherry-locales/' + locale + '.js';
        promises[locale] = new Promise(function (resolve) {
            let script = document.createElement('script');

            script.src = source;
            script.async = true;
            script.onload = function () {
                resolve(cherryLocaleRegistry()[locale] || null);
            };
            script.onerror = function () {
                resolve(null);
            };

            document.head.appendChild(script);
        });

        return promises[locale];
    }

    function cherryDefaultLocales(Cherry) {
        let defaults = Cherry && Cherry.config && Cherry.config.defaults;

        if (!defaults || !defaults.locales || typeof defaults.locales !== 'object') {
            return {};
        }

        return defaults.locales;
    }

    function cherryResolvedLocales(Cherry, locale) {
        let defaults = cherryDefaultLocales(Cherry);
        let localeRegistry = cherryLocaleRegistry();
        let englishBase = defaults.en_US || {};
        let overridePack = localeRegistry[locale];

        if (!overridePack || typeof overridePack !== 'object') {
            return {};
        }

        return {
            [locale]: Object.assign({}, englishBase, overridePack),
        };
    }

    function normalizeHeight(rawHeight) {
        let value = String(rawHeight || '').trim();

        if (value === '') {
            return '200px';
        }

        if (/(px|rem|em|vh|vw|%)$/i.test(value)) {
            return value;
        }

        return value + 'px';
    }

    function sourceTextareas() {
        return Array.prototype.slice.call(document.querySelectorAll('textarea[data-mfw-inputable-cherry="1"]'));
    }

    function hostForTextarea(textarea) {
        let hostId = textarea.getAttribute('data-mfw-inputable-cherry-container');

        if (!hostId) {
            return null;
        }

        return document.getElementById(hostId);
    }

    function syncTextarea(textarea) {
        if (!textarea || !textarea.id) {
            return;
        }

        let record = registry[textarea.id];

        if (!record || !record.instance || typeof record.instance.getMarkdown !== 'function') {
            return;
        }

        textarea.value = record.instance.getMarkdown();
    }

    function syncAll() {
        sourceTextareas().forEach(function (textarea) {
            syncTextarea(textarea);
        });
    }

    function bindSubmitListener() {
        if (submitListenerBound) {
            return;
        }

        document.addEventListener('submit', function () {
            syncAll();
        }, true);

        submitListenerBound = true;
        window.mfwInputableCherrySubmitListenerBound = true;
    }

    function createCherry(textarea, host, locale, editorHeight) {
        let Cherry = cherryConstructor();
        let localizedPacks;
        let options;

        if (typeof Cherry !== 'function') {
            return null;
        }

        localizedPacks = cherryResolvedLocales(Cherry, locale);
        options = {
            id: host.id,
            value: textarea.value || '',
            locale: locale,
            editor: {
                defaultModel: 'edit&preview',
                height: editorHeight,
                convertWhenPaste: true,
                keepDocumentScrollAfterInit: true,
            },
            toolbars: {
                toc: false,
                sidebar: false,
                bubble: false,
                float: false,
            },
            callback: {
                afterInit: function (markdown) {
                    textarea.value = markdown;
                },
                afterChange: function (markdown) {
                    textarea.value = markdown;
                },
            },
        };

        if (Object.keys(localizedPacks).length > 0) {
            options.locales = localizedPacks;
        }

        return new Cherry(options);
    }

    function markReady(textarea, host, instance) {
        if (textarea.id) {
            registry[textarea.id] = {
                textarea: textarea,
                host: host,
                instance: instance,
            };
        }

        if (typeof instance.getMarkdown === 'function') {
            textarea.value = instance.getMarkdown();
        }

        textarea.classList.add('mfw-inputable-cherry-source');
        textarea.setAttribute('data-mfw-inputable-cherry-initialized', '1');
        host.classList.add('is-ready');
        host.style.display = '';
        host.style.visibility = '';

        forceCherryLayout(instance);
    }

    function forceCherryLayout(instance) {
        function activateSplitPreview() {
            if (instance && typeof instance.switchModel === 'function') {
                instance.switchModel('edit&preview');
            }
        }

        function dispatchResize() {
            if (typeof window.dispatchEvent === 'function') {
                window.dispatchEvent(new Event('resize'));
            }
        }

        if (typeof window.requestAnimationFrame === 'function') {
            window.requestAnimationFrame(function () {
                window.requestAnimationFrame(function () {
                    activateSplitPreview();
                    dispatchResize();
                });
            });

            return;
        }

        activateSplitPreview();
        dispatchResize();
    }

    function initTextarea(textarea, locale) {
        let host;
        let initialHeight;
        let editorHeight;
        let instance;

        if (!textarea || textarea.getAttribute('data-mfw-inputable-cherry-initialized') === '1') {
            return;
        }

        host = hostForTextarea(textarea);

        if (!host) {
            return;
        }

        initialHeight = normalizeHeight(textarea.getAttribute('data-mfw-inputable-cherry-height'));
        editorHeight = initialHeight;
        host.style.height = initialHeight;
        host.style.display = 'block';
        host.style.visibility = 'hidden';

        instance = createCherry(textarea, host, locale, editorHeight);

        if (!instance) {
            return;
        }

        markReady(textarea, host, instance);
    }

    function resolvedAppLocale() {
        let appLocale = window.mfwInputableCherryLocale || document.documentElement.lang || 'en';

        return resolveCherryLocale(appLocale);
    }

    function initializeEditors(locale) {
        sourceTextareas().forEach(function (textarea) {
            initTextarea(textarea, locale);
        });

        bindSubmitListener();
    }

    function initAll() {
        let locale;

        if (typeof cherryConstructor() !== 'function') {
            return;
        }

        locale = resolvedAppLocale();
        ensureLocalePack(locale).then(function () {
            initializeEditors(locale);
        }, function () {
            initializeEditors(locale);
        });
    }

    window.mfwInputableInitCherryTextareas = initAll;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initAll();
        });

        return;
    }

    initAll();
})(window, document);
