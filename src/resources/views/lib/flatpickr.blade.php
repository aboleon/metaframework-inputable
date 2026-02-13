@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css"
          integrity="sha256-GzSkJVLJbxDk36qko2cnawOGiqz/Y8GsQv/jMTUrx1Q=" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/themes/airbnb.css">
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"
            integrity="sha256-Huqxy3eUcaCwqqk92RwusapTfWlvAasF6p2rxV6FJaE=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/{{ app()->getLocale() }}.js"></script>
    <script>
        // Preferred config format:
        // data-config="{\"minDate\":\"today\",\"enableTime\":true}"
        //
        // Legacy format (deprecated, will be removed in next major version):
        // data-config="enableTime=true,noCalendar=true,dateFormat=d/m/H H:i,minDate=today"

        let datepickerLegacyConfigWarningShown = false;

        function castLegacyDatepickerValue(value) {
            switch (value) {
                case 'true':
                case '1':
                    return true;
                case 'false':
                case '0':
                    return false;
                default:
                    return value;
            }
        }

        function parseLegacyDatepickerConfig(configString) {
            const parsedConfig = {};
            const entries = configString.split(',');

            for (let i = 0; i < entries.length; i++) {
                const separatorIndex = entries[i].indexOf('=');

                if (separatorIndex === -1) {
                    continue;
                }

                const key = entries[i].slice(0, separatorIndex).trim();
                const value = entries[i].slice(separatorIndex + 1).trim();

                if (!key.length) {
                    continue;
                }

                parsedConfig[key] = castLegacyDatepickerValue(value);
            }

            return parsedConfig;
        }

        function parseDatepickerConfig(customConfig) {
            if (customConfig === undefined || !customConfig.length) {
                return {};
            }

            try {
                const parsedJson = JSON.parse(customConfig);

                if (parsedJson && typeof parsedJson === 'object' && !Array.isArray(parsedJson)) {
                    return parsedJson;
                }
            } catch (error) {
                // Non-JSON config uses the legacy parser below.
            }

            if (!datepickerLegacyConfigWarningShown) {
                console.warn(
                    '[mfw-inputable] Legacy datepicker config syntax is deprecated. Use JSON object syntax instead.');
                datepickerLegacyConfigWarningShown = true;
            }

            return parseLegacyDatepickerConfig(customConfig);
        }

        function setDatepicker() {
            let datepickers = $('.datepicker');
            if (datepickers.length > 0) {
                datepickers.each(function () {
                    if (!$(this).hasClass('flatpickr-input')) {
                        const config = {};
                        config.dateFormat = 'd/m/Y';
                        config.time_24hr = true;
                        config.locale = "{!! app()->getLocale() !!}";

                        if ($(this).attr('id') === undefined) {
                            $(this).attr('id', 'dtpck-' + (Math.random().toString(36).substring(7)));
                        }

                        Object.assign(config, parseDatepickerConfig($(this).attr('data-config')));
                        $(this).flatpickr(config);
                    }
                });
            }
        }

        $(function () {
            setDatepicker();
        });
    </script>
@endpush
