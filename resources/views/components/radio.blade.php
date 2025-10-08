<div class="{{ $class }}">
    @if ($label)
        <label class="form-label d-block">{{ $label }}</label>
    @endif
    @forelse($values as $value => $title)
        <x-mfw-input::input-radio :affected="$affected"
                            :default="$default"
                            :value="$value"
                            :name="$name"
                            :label="str_starts_with($title, 'trans.') ? trans(str_replace('trans.','', $title)) : $title"
                            :params="$params"
        />
    @empty
        {{ __('mfw-input::messages.no_data_provided') }}
    @endforelse
</div>
<x-mfw-input::validation-error :field="$validation_id"/>
