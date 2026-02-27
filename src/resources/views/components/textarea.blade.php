@if ($label)
    <label for="{{ $id }}" class="form-label">
        {{ $label . ($required ? ' *' : '') }}
    </label>
@endif
@php($shouldUseCherry = !$readonly && $mode === 'markdown')
<textarea name="{{ $name }}" class="form-control {{ $class }}" id="{{ $id }}"
    {!! !empty($height) ? 'style="height:' . $height . 'px"' : '' !!}
    @if ($shouldUseCherry) data-mfw-inputable-cherry="1"
        data-mfw-inputable-cherry-container="{{ $cherry_container_id }}"
        data-mfw-inputable-cherry-height="{{ $height }}" @endif
    @forelse($params as $param => $setting)
    @if (is_string($param))
        {{ $param }}="{!! $setting !!}"
    @else
        {!! $setting !!}
    @endif
@empty
@endforelse
    @if ($required) required @endif @if ($readonly) readonly @endif>{!! $value !!}</textarea>
@if ($shouldUseCherry)
    <div id="{{ $cherry_container_id }}" class="mfw-inputable-cherry-host"
        style="display:none; height: {{ $height }}px;">
    </div>
@endif

<x-mfw-inputable::validation-error :field="$validation_id" />

@if ($shouldUseCherry)
    @pushonce('js')
        <style>
            .mfw-inputable-cherry-source {
                display: none !important;
            }

            .mfw-inputable-cherry-host {
                display: none;
                min-height: 160px;
                resize: vertical;
                overflow: auto;
            }

            .mfw-inputable-cherry-host.is-ready {
                display: block;
            }

            .mfw-inputable-cherry-host .cherry {
                border: 1px solid #ced4da;
                border-radius: 0.375rem;
                height: 100% !important;
            }
        </style>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cherry-markdown@0.10.3/dist/cherry-markdown.min.css"
            integrity="sha256-b7u2GY284ovANZf2TRV4x/orn4H8Rr8+EqhmsenHTi4=" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/cherry-markdown@0.10.3/dist/cherry-markdown.min.js"
            integrity="sha256-ZE25m+gKuBt96p3D+KuG9CvtfMU9V7+edi7jyo58BUQ=" crossorigin="anonymous"></script>
        <script src="{!! asset('vendor/mfw-inputable/components/textarea-cherry.js') !!}"></script>
    @endpushonce
@endif
