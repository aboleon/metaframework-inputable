<?php

namespace MetaFramework\Inputable\Components;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use MetaFramework\Inputable\Support\Helpers;

class InputRadio extends Component
{
    public string $id;

    public function __construct(
        public string|int $value,
        public string $name,
        public int|string|null $affected,
        public string $label,
        public int|string|null $default = null,
        public array $params = [],
        public bool $randomize = true,
    ) {
        $this->name = Helpers::generateInputName($this->name);
        $this->id   = Helpers::generateInputId($this->name . ($this->randomize ? '_' . Str::random(8) : ''));
    }

    public function render(): Renderable
    {
        return view('mfw-inputable::components.inputradio');
    }
}
