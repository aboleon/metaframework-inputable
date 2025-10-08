<?php

namespace MetaFramework\Inputable\Components;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\View\Component;

class InputDateMask extends Component
{
    public function __construct(
        public string $name,
        public int|string|float|null $value = '',
        public string $type = 'text',
        public array $params = [],
        public string|null $label = '',
        public string $class = '',
        public bool $required = false,
        public bool $readonly = false,
    ) {
        $this->class = rtrim('inputdatemask ' . $this->class . ' ');
    }

    public function render(): Renderable
    {
        $locale = App::getLocale();
        $this->params = array_merge($this->params, [
            'maxlength' => 10,
            'placeholder' => Lang::get('mfw-input::messages.date_placeholder', locale: $locale),
        ]);

        return view('mfw-input::components.inputdatemask')->with([
            'label' => $this->label,
            'class' => $this->class,
            'required' => $this->required,
            'value' => $this->value,
            'params' => $this->params,
        ]);
    }
}
