<?php

namespace MetaFramework\Inputable\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use MetaFramework\Inputable\Contracts\GooglePlacesInterface;

class GooglePlaces extends Component
{
    /**
     * Additional parameters for the Google Places JS URL.
     *
     * @example ['types' => '(cities)']
     */
    private const READONLY_FIELDS = ['street_number', 'route', 'locality', 'postal_code', 'country'];
    private const COORDINATE_FIELDS = ['lat', 'lon'];

    public ?GooglePlacesInterface $geo = null;
    public ?string $defaultTextAddress = null;
    public ?string $error = null;
    public Collection $required;

    public function __construct(
        ?object $model = null,
        ?object $geo = null,
        public string $field = 'mfw_geo',
        public string $random_id = '',
        public array $params = [],
        public string $placeholder = '',
        public string $tag_required = 'required',
        public ?string $label = null,
        public ?string $notice = null,
        public array $hidden = [
            'administrative_area_level_1_short',
            'administrative_area_level_1',
            'administrative_area_level_2',
            'country_code',
        ],
        public bool $showCoords = false,
    ) {
        $this->required = collect($this->params['required'] ?? []);
        $resolvedModel = $model ?? $geo;

        if (!$resolvedModel instanceof GooglePlacesInterface) {
            $this->error = 'The passed model must implement ' . GooglePlacesInterface::class;
            return;
        }

        $this->geo = $resolvedModel;
        $this->random_id = Str::random(4);
        $this->defaultTextAddress = $this->geo->text_address ?? $this->geo->locality;

        if (!$this->showCoords) {
            $this->hidden = array_merge($this->hidden, self::COORDINATE_FIELDS);
        }
    }

    public function render(): View
    {
        if ($this->error) {
            return view('mfw-support::components.alert', [
                'type' => 'warning',
                'message' => $this->error,
            ]);
        }

        return view('mfw-inputable::components.google-places');
    }

    public function tagRequired(string $key): string
    {
        return $this->required->contains($key) ? $this->tag_required : '';
    }

    public function labelRequired(string $key): string
    {
        return $this->required->contains($key) ? ' *' : '';
    }

    public function inputable(string $key): string
    {
        return 'col-' . $key . (in_array($key, $this->hidden) ? ' d-none' : '');
    }

    public function readonlies(string $key): string
    {
        if ($this->defaultTextAddress || !in_array($key, self::READONLY_FIELDS)) {
            return '';
        }

        return ' lockable';
    }
}
