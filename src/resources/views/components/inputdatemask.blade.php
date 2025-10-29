<x-mfw-inputable::input :name="$name" :value="$value" :label="$label" :class="$class" :required="$required" :params="$params" :readonly="$readonly" />
@pushonce('js')
<script src="{{ asset('vendor/mfw-inputable/components/inputdatemask.js') }}"></script>
@endpushonce
