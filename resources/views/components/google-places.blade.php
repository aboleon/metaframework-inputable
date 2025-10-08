{{-- Pour activer la recherche Google Maps Places - class=gmpasbar --}}
@php
    $error = $errors->any();
    $resolver = config('mfw-input.countries_resolver');
    $countryName = '';
    if ($error) {
        $countryName = old($field . '.country');
    } elseif (!empty($geo->country)) {
        $countryName = $geo->country;
    } elseif (!empty($geo->country_code) && $resolver && class_exists($resolver) && method_exists($resolver, 'getCountryNameByCode')) {
        $countryName = $resolver::getCountryNameByCode($geo->country_code);
    }
@endphp
<div class="clearfix gmapsbar {{ $field }}" id="mapsbar_{{ $random_id }}">
    <div class="locationField" data-error="">

        @if ($label)
            <label for="geo_text_address_{{ $random_id }}"
                   class="form-label">{{ $label . $labelRequired('text_address') }}</label>
        @endif
        <input type="text"
               name="{{ $field }}[text_address]"
               value="{{ $error ? old($field.'.text_address') : $defaultTextAdress }}"
               class="g_autocomplete form-control {{ $tagRequired('text_address') }}"
               id="geo_text_address_{{ $random_id }}"
               placeholder="{{ $placeholder ?: trans('mfw-input::geo.type_address') }}" {{ $tagRequired('text_address') }}>

        @if ($notice)
            <small class="pt-1 d-block text-secondary">{{ $notice }}</small>
        @endif
        <x-mfw-input::validation-error field="{{ $field }}[text_address]"/>
    </div>

    <div class="my-3 row {{ $field }}_fields">
        <div class="mb-3 col-sm-4 {{ $inputable('street_number') }}">
            <x-mfw-input::input class="field street_number{{ $tagRequired('street_number') . $readonlies('street_number') }}"
                                :label="trans('mfw-input::geo.street_number')"
                                name="{{ $field }}[street_number]"
                                value="{{ $error ? old($field.'.street_number') : ($geo->street_number ?? '') }}"
                                :params="['placeholder'=> trans('mfw-input::geo.street_number')]"
                                :required="$tagRequired('street_number')"
                                :readonly="$readonlies('street_number')"
            />

        </div>
        <div class="mb-3 col-sm-8 {{ $inputable('route') }}">
            <x-mfw-input::input class="field route{{ $tagRequired('route') . $readonlies('route') }}"
                                :label="trans('mfw-input::geo.route') . $labelRequired('route')"
                                name="{{ $field }}[route]"
                                value="{{ $error ? old($field.'.route') : ($geo->route ?? '') }}"
                                :params="['placeholder'=> trans('mfw-input::geo.route')]"
                                :readonly="$readonlies('route')"
            />
        </div>
        <div class="mb-3 col-sm-4 {{ $inputable('postal_code') }}">
            <x-mfw-input::input class="field postal_code{{ $tagRequired('postal_code') . $readonlies('postal_code') }}"
                                :label="trans('mfw-input::geo.postal_code') . $labelRequired('postal_code')"
                                name="{{ $field }}[postal_code]"
                                value="{{ $error ? old($field.'.postal_code') : ($geo->postal_code ?? '') }}"
                                :params="['placeholder'=> trans('mfw-input::geo.postal_code')]"
                                :readonly="$readonlies('postal_code')"
            />
        </div>
        <div class="mb-3 col-sm-8 {{ $inputable('locality') }}">
            <x-mfw-input::input class="field locality{{ $tagRequired('locality') . $readonlies('locality') }}"
                                :label="trans('mfw-input::geo.locality') . $labelRequired('locality')"
                                name="{{ $field }}[locality]"
                                value="{{ $error ? old($field.'.locality') : ($geo->locality ?? '') }}"
                                :params="['placeholder'=> trans('mfw-input::geo.locality')]"
                                :readonly="$readonlies('locality')"/>
        </div>
        <div class="mb-3 col-sm-4 {{ $inputable('administrative_area_level_2') }}">
            <x-mfw-input::input class="field administrative_area_level_2 {{ $tagRequired('administrative_area_level_2') }}"
                                :label="trans('mfw-input::geo.district') . $labelRequired('administrative_area_level_2')"
                                name="{{ $field }}[administrative_area_level_2]"
                                value="{{ $error ? old($field.'.administrative_area_level_2') : ($geo->administrative_area_level_2 ?? '') }}"/>
        </div>
        <div class="mb-3 col-sm-8 {{ $inputable('administrative_area_level_1') }}">
            <x-mfw-input::input class="field administrative_area_level_1 {{ $tagRequired('administrative_area_level_1') }}"
                                :label="trans('mfw-input::geo.region') . $labelRequired('administrative_area_level_1')"
                                name="{{ $field }}[administrative_area_level_1]"
                                value="{{ $error ? old($field.'.administrative_area_level_1') : ($geo->administrative_area_level_1 ?? '') }}"/>
        </div>
        <div class="mb-3 col-sm-8 {{ $inputable('administrative_area_level_1_short') }}">
            <x-mfw-input::input
                    class="field administrative_area_level_1_short {{ $tagRequired('administrative_area_level_1_short') }}"
                    :label="trans('mfw-input::geo.region') . $labelRequired('administrative_area_level_1_short')"
                    name="{{ $field }}[administrative_area_level_1_short]"
                    value="{{ $error ? old($field.'.administrative_area_level_1_short') : ($geo->administrative_area_level_1_short ?? '') }}"/>
        </div>
        <div class="mb-3 col-sm-2 {{ $inputable('country_code') }}">
            <x-mfw-input::input
                    class="field country_code {{ $tagRequired('country_code') }}"
                    :label="trans('mfw-input::geo.country_code') . $labelRequired('country_code')"
                    name="{{ $field }}[country_code]"
                    value="{{ $error ? old($field.'.country_code') : ($geo->country_code ?? '') }}"/>
        </div>
        <div class="mb-3 col-sm-5 {{ $inputable('country') }}">
            <x-mfw-input::input
                    class="field country {{ $tagRequired('country') }}"
                    :label="trans('mfw-input::geo.country') . $labelRequired('country')"
                    name="{{ $field }}[country]"
                    value="{{ $countryName }}"
                    readonly/>
        </div>
    </div>
    <input type="hidden" class="wa_geo_lat" name="{{ $field }}[lat]"
           value="{{ $error ? old($field.'.lat') : ($geo->lat ?? '') }}"/>
    <input type="hidden" class="wa_geo_lon" name="{{ $field }}[lon]"
           value="{{ $error ? old($field.'.lon') : ($geo->lon ?? '') }}"/>
    <input type="hidden" class="address_type" name="{{ $field }}[address_type]"/>
    <input type="hidden" class="continent" name="{{ $field }}[continent]"/>
</div>
@if ($params)
    <span id="params_mapsbar_{{ $random_id }}" class="d-none">{!! collect($params)->toJson() !!}</span>
@endif
@once
    @push('js')
        <script src="{{ asset('vendor/mfw-input/components/google-places-geolocate.js') }}"></script>
        @php($placesKey = config('mfw-input.google.places_api_key'))
        @if($placesKey)
            <script src="https://maps.googleapis.com/maps/api/js?key={{ $placesKey }}&libraries=places&callback=initialize"></script>
        @endif
    @endpush
@endonce
