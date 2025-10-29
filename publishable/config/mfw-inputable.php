<?php

return [
    'google' => [
        'places_api_key' => env('MFW_INPUTABLE_GOOGLE_PLACES_KEY', env('MFW_INPUT_GOOGLE_PLACES_KEY', '')),
    ],
    'countries_resolver' => null,
];
