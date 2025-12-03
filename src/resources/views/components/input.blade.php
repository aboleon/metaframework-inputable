@if ($wrap)
    <{!! $wrap . ($class ? ' class="'.$class.'"' : '') !!}>
@endif

@if ($label)
    <label for="{{ $id }}" class="form-label">{!! $label . ($required ? ' *' : '') !!}</label>
@endif

@if ($prefix or $suffix)
    <div class="input-group">
        @if($prefix)
            <span class="input-group-text" id="basic-addon-{{ $id }}">{!! $prefix !!}</span>
        @endif
        @endif

        <input type="{{ $type ?? 'text' }}"
               name="{{ $name }}"
               class="form-control {{ !$wrap && $class ? $class : '' }}"
               id="{{ $id }}"
               value="{{ $value }}"
        @foreach($params as $param => $setting)
            @if (is_string($param))
                {{ $param }}="{!! $setting !!}"
            @else
                {!! $setting !!}
            @endif
        @endforeach
        @if($required)
            required
        @endif
        @if($readonly)
            readonly
        @endif
        >
        @if ($prefix or $suffix)

            @if($suffix)
                <span class="input-group-text" id="basic-addon-{{ $id }}">{!! $suffix !!}</span>
            @endif
    </div>
@endif
<x-mfw-inputable::validation-error :field="$validation_id"/>
@if ($wrap)
    </{{ $wrap }}>
@endif
