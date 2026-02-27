<?php

declare(strict_types=1);

namespace MetaFramework\Inputable\Components;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use MetaFramework\Inputable\Enum\ContentTypeEnum;
use MetaFramework\Inputable\Support\Helpers;

class Textarea extends Component
{
    private string $id;

    private string $validation_id;

    private string $cherryContainerId;

    public string $tinymcePreset = 'simplified';

    public bool $shouldUseCherry = false;

    public bool $shouldActivateTinymce = false;

    public function __construct(
        public string $name,
        public ?string $value = null,
        public ?string $label = null,
        public string|array $class = '',
        public array $params = [],
        public int $height = 200,
        public string $mode = ContentTypeEnum::TEXT->value,
        public bool $required = false,
        public bool $readonly = false,
        public bool $randomize = false,
        public bool|string|null $contentType = null,
    ) {
        $this->id = Helpers::generateInputId($this->name . ($this->randomize ? '_' . Str::random(8) : ''));
        $this->cherryContainerId = sprintf('%s_cherry', $this->id);
        $this->validation_id = Helpers::generateValidationId($this->name);
        $this->name = Helpers::generateInputName($this->name);

        $classTokens = $this->normalizeClassTokens($this->class);
        $this->tinymcePreset = $this->detectTinymcePreset($classTokens) ?? 'simplified';
        $this->class = implode(' ', $classTokens);

        if (array_key_exists('height', $this->params)) {
            $this->height = (int) $this->params['height'];
        }

        $this->mode = $this->resolveMode($classTokens);
        $this->shouldUseCherry = !$this->readonly && $this->mode === ContentTypeEnum::MARKDOWN->value;
        $this->shouldActivateTinymce = !$this->readonly && $this->mode === ContentTypeEnum::HTML->value;
    }

    public function render(): Renderable
    {
        return view('mfw-inputable::components.textarea')->with([
            'id' => $this->id,
            'cherry_container_id' => $this->cherryContainerId,
            'validation_id' => $this->validation_id,
        ]);
    }

    private function resolveMode(array $classTokens): string
    {
        if ($this->contentType === true) {
            return $this->hasEditorClass($classTokens)
                ? ContentTypeEnum::HTML->value
                : ContentTypeEnum::default();
        }

        if (is_string($this->contentType)) {
            return ContentTypeEnum::resolve($this->contentType)?->value ?? ContentTypeEnum::default();
        }

        return ContentTypeEnum::resolve($this->mode)?->value ?? ContentTypeEnum::default();
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

    private function hasEditorClass(array $classTokens): bool
    {
        return in_array('simplified', $classTokens, true)
            || in_array('extended', $classTokens, true);
    }

    private function detectTinymcePreset(array $classTokens): ?string
    {
        if (in_array('extended', $classTokens, true)) {
            return 'extended';
        }

        if (in_array('simplified', $classTokens, true)) {
            return 'simplified';
        }

        return null;
    }
}
