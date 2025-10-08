<?php

namespace MetaFramework\Inputable\Components;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\Component;

class Datepicker extends Component
{
    /**
     * Example for $config: dateFormat=d/m/Y
     */
    public function __construct(
        public string $name,
        public ?string $value = null,
        public string $format = 'd/m/Y',
        public ?string $config = null,
        public string|null $label = '',
        public ?string $class = null,
        public bool $required = false,
        public array $params = [],
        public bool $randomize = false
    ) {
        $this->class = rtrim('datepicker ' . ($this->class ?? '') . ' ');
    }

    public function render(): Renderable
    {
        $baseParams = [
            'data-date-format' => $this->format,
            'placeholder' => __('mfw-input::messages.select_date'),
        ];

        $this->params = array_merge($baseParams, $this->params);

        if ($this->config) {
            $this->params['data-config'] = $this->config;
        }

        return view('mfw-input::components.datepicker')->with([
            'randomize' => $this->randomize,
            'label' => $this->label,
            'class' => $this->class,
            'required' => $this->required,
            'value' => $this->value,
            'params' => $this->params,
        ]);
    }
}
