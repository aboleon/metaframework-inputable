<?php

namespace MetaFramework\Inputable\Components;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use MetaFramework\Inputable\Support\Helpers;

class Select extends Component
{
    private string $id;
    private string $validation_id;

    public function __construct(
        public string|array $values,
        public string $name,
        public int|string|null $affected = null,
        public string $label = '',
        public bool $nullable = true,
        public bool $disablename = false,
        public string $defaultselecttext = '',
        public bool $group = false,
        public ?string $class = null,
        public array $params = [],
        public ?string $identifier = null,
        public bool $randomize = false,
    ) {
        $this->defaultselecttext = $this->defaultselecttext ?: '---  ' . __('mfw-inputable-messages.select_option') . ' ---';
        $this->class = rtrim('form-control form-select ' . ($this->class ?? ''));

        $this->id             = Helpers::generateInputId($this->name . '_' . $this->identifier . ($this->randomize ? '_' . Str::random(8) : ''));
        $this->validation_id  = Helpers::generateValidationId($this->name);
        $this->name           = Helpers::generateInputName($this->name);
        $this->label          = array_key_exists('required', $this->params) ? $this->label . ' * ' : $this->label;
    }

    public function render(): Renderable
    {
        return view('mfw-inputable::components.select')->with([
            'id' => $this->id,
            'validation_id' => $this->validation_id,
        ]);
    }
}
