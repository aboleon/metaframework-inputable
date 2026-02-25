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

    public bool $shouldActivateTinymce = false;

    public function __construct(
        public string $name,
        public ?string $value = null,
        public ?string $label = null,
        public string|array $class = '',
        public array $params = [],
        public int $height = 200,
        public bool $required = false,
        public bool $readonly = false,
        public bool $randomize = false,
        public ?string $type = null,
    ) {
        $this->id            = Helpers::generateInputId($this->name . ($this->randomize ? '_' . Str::random(8) : ''));
        $this->validation_id = Helpers::generateValidationId($this->name);
        $this->name          = Helpers::generateInputName($this->name);

        if (array_key_exists('height', $this->params)) {
            $this->height = $this->params['height'];
        }

        $this->configureContentTypeBehavior();
    }

    public function render(): Renderable
    {
        return view('mfw-inputable::components.textarea')->with([
            'id' => $this->id,
            'validation_id' => $this->validation_id,
        ]);
    }

    private function configureContentTypeBehavior(): void
    {
        $resolvedType = ContentTypeEnum::resolve($this->type);
        $classTokens = $this->normalizeClassTokens($this->class);
        $hasEditorClass = $this->hasEditorClass($classTokens);

        if ($resolvedType === ContentTypeEnum::HTML && !$hasEditorClass) {
            $classTokens[] = 'simplified';
            $hasEditorClass = true;
        }

        $this->shouldActivateTinymce = match ($resolvedType) {
            ContentTypeEnum::TEXT, ContentTypeEnum::MARKDOWN => false,
            ContentTypeEnum::HTML => true,
            null => $hasEditorClass,
        };

        $this->type = $resolvedType?->value;
        $this->class = implode(' ', $classTokens);
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
}
