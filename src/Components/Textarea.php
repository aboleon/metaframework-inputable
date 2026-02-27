<?php

declare(strict_types=1);

namespace MetaFramework\Inputable\Components;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use MetaFramework\Inputable\Support\Helpers;

class Textarea extends Component
{
    private string $id;

    private string $validation_id;

    private string $cherryContainerId;

    private const MODE_MARKDOWN = 'markdown';

    private const MODE_PLAIN = 'plain';

    public function __construct(
        public string $name,
        public ?string $value = null,
        public ?string $label = null,
        public string|array $class = '',
        public array $params = [],
        public int $height = 200,
        public string $mode = self::MODE_MARKDOWN,
        public bool $required = false,
        public bool $readonly = false,
        public bool $randomize = false,
    ) {
        $this->id = Helpers::generateInputId($this->name . ($this->randomize ? '_' . Str::random(8) : ''));
        $this->cherryContainerId = sprintf('%s_cherry', $this->id);
        $this->validation_id = Helpers::generateValidationId($this->name);
        $this->name = Helpers::generateInputName($this->name);
        $this->class = implode(' ', $this->normalizeClassTokens($this->class));

        if (array_key_exists('height', $this->params)) {
            $this->height = $this->params['height'];
        }
        $this->mode = $this->normalizeMode($this->mode);
    }

    public function render(): Renderable
    {
        return view('mfw-inputable::components.textarea')->with([
            'id' => $this->id,
            'cherry_container_id' => $this->cherryContainerId,
            'validation_id' => $this->validation_id,
        ]);
    }

    private function normalizeClassTokens(string|array $class): array
    {
        $rawTokens = is_array($class)
            ? $class
            : (preg_split('/\s+/', trim($class)) ?: []);

        return collect($rawTokens)
            ->filter(fn ($token) => is_string($token) && trim($token) !== '')
            ->map(fn (string $token) => trim($token))
            ->unique()
            ->values()
            ->all();
    }

    private function normalizeMode(string $mode): string
    {
        $normalizedMode = strtolower(trim($mode));

        if ($normalizedMode === self::MODE_PLAIN) {
            return self::MODE_PLAIN;
        }

        return self::MODE_MARKDOWN;
    }
}
