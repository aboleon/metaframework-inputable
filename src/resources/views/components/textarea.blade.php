@if ($label)
    <label for="{{ $id }}" class="form-label">
        {{ $label . ($required ? ' *' : '') }}
    </label>
@endif
@if ($contentTypeEnabled)
    <x-mfw-inputable::radio :name="$contentTypeRadioName" :values="$contentTypeOptions" :affected="$contentTypeValue" class="mb-2 p-0" :randomize="false"
        :params="['data-mfw-inputable-content-type-radio' => $id]" />
    <input type="hidden" id="{{ $contentTypeHiddenId }}" name="{{ $contentTypeHiddenName }}"
        value="{{ $contentTypeValue }}" data-mfw-inputable-content-type-hidden="{{ $id }}">
@endif
<textarea name="{{ $name }}" class="form-control {{ $class }}" id="{{ $id }}"
    {!! !empty($height) ? 'style="height:' . $height . 'px"' : '' !!}
    @if ($contentTypeEnabled) data-mfw-inputable-content-type-enabled="1"
          data-mfw-inputable-tinymce-preset="{{ $tinymcePreset }}" @endif
    @forelse($params as $param => $setting)
    @if (is_string($param))
        {{ $param }}="{!! $setting !!}"
    @else
        {!! $setting !!}
    @endif
@empty
@endforelse
    @if ($required) required @endif @if ($readonly) readonly @endif>{!! $value !!}</textarea>

<x-mfw-inputable::validation-error :field="$validation_id" />

@if ($shouldActivateTinymce || $contentTypeEnabled)
    @pushonce('js')
        <style>
            .tox.tox-tinymce.tox-edit-focus .tox-edit-area::before {
                border-color: #b4c4d0 !important;
            }
        </style>
        <script src="https://cdn.jsdelivr.net/npm/tinymce@8.3.2/tinymce.min.js"
            integrity="sha256-7MK838XEuRxsjf+kLlySGcX6FL3X8UeAJeoQpIy0snc=" crossorigin="anonymous"></script>
        <script id="tinymce_settings" src="{!! asset('vendor/mfw-inputable/js/tinymce/default_settings.js') !!}"></script>
        <script src="{!! asset('vendor/mfw-inputable/components/textarea-content-type.js') !!}"></script>
    @endpushonce
@endif
