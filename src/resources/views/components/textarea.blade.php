@if ($label)
    <label for="{{$id}}" class="form-label">
        {{ $label  . ($required ? ' *' : '') }}
    </label>
@endif
<textarea name="{{ $name }}"
          class="form-control {{ is_array($class) ? explode(' ', $class) : $class }}"
          id="{{ $id }}"
{!! !empty($height) ? 'style="height:'.$height.'px"' : '' !!}
@forelse($params as $param => $setting)
    @if (is_string($param))
        {{ $param }}="{!! $setting !!}"
    @else
        {!! $setting !!}
    @endif
@empty
@endforelse
@if($required)
    required
@endif
@if($readonly)
    readonly
@endif
>{!! $value !!}</textarea>

<x-mfw-inputable::validation-error :field="$validation_id"/>


@if(str_contains($class,'simplified') or str_contains($class, 'extended'))
    @pushonce('js')
        <script src="https://cdn.jsdelivr.net/npm/tinymce@8.3.2/tinymce.min.js" integrity="sha256-7MK838XEuRxsjf+kLlySGcX6FL3X8UeAJeoQpIy0snc=" crossorigin="anonymous"></script>
        <script id="tinymce_settings" src="{!! asset('vendor/mfw-inputable/js/tinymce/default_settings.js') !!}"></script>
        <script>
          if ($('textarea.extended').length) {
            tinymce.init(mfw_default_tinymce_settings('textarea.extended'));
          }
          $(function() {
            if ($('textarea.simplified').length) {
              var url = "{!! asset('vendor/mfw-inputable/js/tinymce/simplified.js') !!}";
              $.when($.getScript(url)).then(function() {
                tinymce.init(mfw_simplified_tinymce_settings('textarea.simplified'));
              });
            }
          });
        </script>
    @endpushonce
@endif
