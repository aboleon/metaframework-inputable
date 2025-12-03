<div class="{{ $class }}">
    @if ($label)
        <label class="form-label d-block {{ $labelclass }}">{!! $label !!}</label>
    @endif
    @forelse($values as $value => $title)
        <x-mfw-inputable::input-radio :affected="$affected"
                            :default="$default"
                            :value="$value"
                            :name="$name"
                            :label="str_starts_with($title, 'trans.') ? trans(str_replace('trans.','', $title)) : $title"
                            :params="$params"
        />
    @empty
        {{ __('mfw-inputable-messages.no_data_provided') }}
    @endforelse
</div>
<x-mfw-inputable::validation-error :field="$validation_id"/>
