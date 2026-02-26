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

    public bool $contentTypeEnabled = false;

    public string $contentTypeValue = '';

    public string $contentTypeHiddenName = '';

    public string $contentTypeHiddenId = '';

    public string $contentTypeRadioName = '';

    public array $contentTypeOptions = [];

    public string $tinymcePreset = 'simplified';

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
        public bool|string $contentType = false,
    ) {
        $this->id            = Helpers::generateInputId($this->name . ($this->randomize ? '_' . Str::random(8) : ''));
        $this->validation_id = Helpers::generateValidationId($this->name);
        $this->name          = Helpers::generateInputName($this->name);

        if (array_key_exists('height', $this->params)) {
            $this->height = $this->params['height'];
        }

        $this->removeLegacyTypeParam();
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
        $classTokens = $this->normalizeClassTokens($this->class);
        $this->tinymcePreset = $this->detectTinymcePreset($classTokens) ?? 'simplified';
        $hasEditorClass = $this->hasEditorClass($classTokens);
        $this->contentTypeEnabled = $this->shouldEnableContentTypeSelector();

        if ($this->contentTypeEnabled) {
            $effectiveType = $this->resolveContentTypeValueFromParam() ?? ($hasEditorClass
                ? ContentTypeEnum::HTML->value
                : ContentTypeEnum::default());

            if ($effectiveType === ContentTypeEnum::HTML->value && !$hasEditorClass) {
                $classTokens[] = $this->tinymcePreset;
                $hasEditorClass = true;
            }

            $this->contentTypeValue = $effectiveType;
            $this->contentTypeOptions = [
                ContentTypeEnum::HTML->value => 'HTML',
                ContentTypeEnum::MARKDOWN->value => 'Markdown',
                ContentTypeEnum::TEXT->value => 'Text',
            ];
            $this->contentTypeHiddenName = sprintf('mfw-inputable[content_type][%s]', $this->name);
            $this->contentTypeHiddenId = sprintf('%s_content_type_hidden', $this->id);
            $this->contentTypeRadioName = sprintf('%s_content_type_selector', $this->id);
        }

        $this->shouldActivateTinymce = $this->contentTypeEnabled
            ? $this->contentTypeValue === ContentTypeEnum::HTML->value
            : $hasEditorClass;
        $this->class = implode(' ', $classTokens);
    }

    private function removeLegacyTypeParam(): void
    {
        if (array_key_exists('type', $this->params)) {
            unset($this->params['type']);
        }
    }

    private function shouldEnableContentTypeSelector(): bool
    {
        return $this->contentType === true || is_string($this->contentType);
    }

    private function resolveContentTypeValueFromParam(): ?string
    {
        if (!is_string($this->contentType)) {
            return null;
        }

        $normalized = strtolower(trim($this->contentType));

        if ($normalized === '') {
            return null;
        }

        return in_array($normalized, ContentTypeEnum::values(), true)
            ? $normalized
            : null;
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
