<x-mfw-input::input :name="$name" :value="$value" :label="$label" :class="$class" :required="$required" :params="$params" :readonly="$readonly" />
@pushonce('js')
<script src="{{ asset('vendor/mfw-input/components/inputdatemask.js') }}"></script>
@endpushonce
