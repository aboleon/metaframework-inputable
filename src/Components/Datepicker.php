<?php

declare(strict_types=1);

namespace MetaFramework\Inputable\Components;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\Component;

class Datepicker extends Component
{
    /**
     * Example for $config:
     * - string: dateFormat=d/m/Y
     * - array: ['dateFormat' => 'd/m/Y', 'minDate' => 'today']
     *
     * Legacy string syntax is deprecated and should be migrated to array syntax.
     */
    public function __construct(
        public string $name,
        public ?string $value = null,
        public string $format = 'd/m/Y',
        public array|string|null $config = null,
        public ?string $label = '',
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
            'placeholder' => __('mfw-inputable-messages.select_date'),
        ];

        $this->params = array_merge($baseParams, $this->params);

        if ($this->config !== null) {
            $this->params['data-config'] = $this->stringifyConfig($this->config);
        }

        return view('mfw-inputable::components.datepicker')->with([
            'randomize' => $this->randomize,
            'label' => $this->label,
            'class' => $this->class,
            'required' => $this->required,
            'value' => $this->value,
            'params' => $this->params,
        ]);
    }

    private function stringifyConfig(array|string $config): string
    {
        if (is_string($config)) {
            return $config;
        }

        return htmlspecialchars(
            json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}',
            ENT_QUOTES,
            'UTF-8'
        );
    }
}
