<?php

declare(strict_types=1);

namespace MetaFramework\Inputable\Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use MetaFramework\Inputable\Tests\TestCase;

class BladeComponentsRenderTest extends TestCase
{
    public function test_input_component_renders_label_wrap_prefix_suffix_and_attributes(): void
    {
        $html = Blade::render(
            '<x-mfw-inputable::input name="user.email" label="Email" prefix="@" suffix=".com" wrap="div" class="mb-2" required readonly />'
        );

        $this->assertStringContainsString('<div class="mb-2">', $html);
        $this->assertStringContainsString('<label for="user_email" class="form-label">Email *', $html);
        $this->assertStringContainsString('name="user[email]"', $html);
        $this->assertStringContainsString('class="form-control "', $html);
        $this->assertStringContainsString('<div class="input-group">', $html);
        $this->assertStringContainsString('input-group-text', $html);
        $this->assertStringContainsString('required', $html);
        $this->assertStringContainsString('readonly', $html);
    }

    public function test_number_component_renders_number_input_with_min_max_step(): void
    {
        $html = Blade::render(
            '<x-mfw-inputable::number name="qty" :value="5" :min="1" :max="10" step="2" prefix="$" />'
        );

        $this->assertStringContainsString('type="number"', $html);
        $this->assertStringContainsString('min="1"', $html);
        $this->assertStringContainsString('max="10"', $html);
        $this->assertStringContainsString('step="2"', $html);
        $this->assertStringContainsString('value="5"', $html);
    }

    public function test_textarea_component_renders_height_and_value_and_validation_error(): void
    {
        $errors = new ViewErrorBag;
        $errors->put('default', new MessageBag(['notes' => ['Required']]));
        View::share('errors', $errors);

        $html = Blade::render(
            '<x-mfw-inputable::textarea name="notes" label="Notes" :value="$value" :height="120" class="simplified" required />@stack("js")',
            ['value' => 'Hello', 'errors' => $errors]
        );

        $this->assertStringContainsString('name="notes"', $html);
        $this->assertStringContainsString('style="height:120px"', $html);
        $this->assertStringContainsString('>Hello</textarea>', $html);
        $this->assertStringContainsString('invalid-feedback d-block', $html);
        $this->assertStringContainsString('tinymce.min.js', $html);
    }

    public function test_textarea_type_text_disables_tinymce_even_with_editor_class(): void
    {
        $html = Blade::render(
            '<x-mfw-inputable::textarea name="notes" class="simplified" type="text" />@stack("js")'
        );

        $this->assertStringContainsString('class="form-control simplified"', $html);
        $this->assertStringNotContainsString('tinymce.min.js', $html);
    }

    public function test_textarea_type_html_applies_simplified_when_no_editor_class_and_enables_tinymce(): void
    {
        $html = Blade::render(
            '<x-mfw-inputable::textarea name="notes" type="html" />@stack("js")'
        );

        $this->assertStringContainsString('class="form-control simplified"', $html);
        $this->assertStringContainsString('tinymce.min.js', $html);
    }

    public function test_textarea_type_markdown_disables_tinymce(): void
    {
        $html = Blade::render(
            '<x-mfw-inputable::textarea name="notes" class="extended" type="markdown" />@stack("js")'
        );

        $this->assertStringContainsString('class="form-control extended"', $html);
        $this->assertStringNotContainsString('tinymce.min.js', $html);
    }

    public function test_textarea_can_read_type_from_params_and_does_not_render_type_attribute(): void
    {
        $html = Blade::render(
            '<x-mfw-inputable::textarea name="notes" class="simplified" :params="$params" />@stack("js")',
            ['params' => ['type' => 'text', 'data-x' => '1']]
        );

        $this->assertStringContainsString('class="form-control simplified"', $html);
        $this->assertStringContainsString('data-x="1"', $html);
        $this->assertStringNotContainsString(' type="text"', $html);
        $this->assertStringNotContainsString('tinymce.min.js', $html);
    }

    public function test_select_component_renders_nullable_option_and_selected_value(): void
    {
        $this->ensureGlobalTranslations();

        $html = Blade::render(
            '<x-mfw-inputable::select name="status" :values="$values" :affected="$affected" />',
            ['values' => ['draft' => 'Draft', 'live' => 'Live'], 'affected' => 'live']
        );

        $this->assertStringContainsString('<option value="">---  Select ---</option>', $html);
        $this->assertStringContainsString('value="live" selected', $html);
    }

    public function test_select_component_renders_grouped_options(): void
    {
        $this->ensureGlobalTranslations();

        $values = [
            'group-1' => [
                'name' => 'Group 1',
                'values' => [
                    'a' => 'Alpha',
                    'b' => 'Beta',
                ],
            ],
        ];

        $html = Blade::render(
            '<x-mfw-inputable::select name="grouped" :values="$values" :group="true" />',
            ['values' => $values]
        );

        $this->assertStringContainsString('<optgroup data-id="group-1" label="Group 1">', $html);
        $this->assertStringContainsString('value="a"', $html);
        $this->assertStringContainsString('value="b"', $html);
    }

    public function test_checkbox_component_renders_checked_and_switch(): void
    {
        $html = Blade::render(
            '<x-mfw-inputable::checkbox name="active" :affected="true" label="Active" :switch="true" :randomize="false" />'
        );

        $this->assertStringContainsString('type="checkbox"', $html);
        $this->assertStringContainsString('role="switch"', $html);
        $this->assertStringContainsString('checked', $html);
        $this->assertStringContainsString('Active', $html);
    }

    public function test_radio_component_renders_options_and_translation_labels(): void
    {
        $this->ensureGlobalTranslations();

        $values = [
            'yes' => 'Yes',
            'no' => 'trans.mfw-inputable-messages.select_option',
        ];

        $html = Blade::render(
            '<x-mfw-inputable::radio name="choice" :values="$values" :affected="$affected" />',
            ['values' => $values, 'affected' => 'yes']
        );

        $this->assertStringContainsString('type="radio"', $html);
        $this->assertStringContainsString('value="yes"', $html);
        $this->assertStringContainsString('Select', $html);
    }

    public function test_radio_component_renders_empty_state(): void
    {
        $this->ensureGlobalTranslations();

        $html = Blade::render(
            '<x-mfw-inputable::radio name="choice" :values="$values" :affected="$affected" />',
            ['values' => [], 'affected' => null]
        );

        $this->assertStringContainsString('No data provided', $html);
    }

    public function test_input_radio_component_marks_default_when_no_affected(): void
    {
        $html = Blade::render(
            '<x-mfw-inputable::input-radio name="flag" value="on" label="On" :default="$default" :randomize="false" />',
            ['default' => 'on']
        );

        $this->assertStringContainsString('type="radio"', $html);
        $this->assertStringContainsString('checked', $html);
        $this->assertStringContainsString('On', $html);
    }

    public function test_datepicker_component_renders_input_with_date_attributes(): void
    {
        $html = Blade::render(
            '<x-mfw-inputable::datepicker name="start_date" :value="$value" format="Y-m-d" config="enableTime=true" />',
            ['value' => '2025-01-01']
        );

        $this->assertStringContainsString('class="form-control datepicker', $html);
        $this->assertStringContainsString('data-date-format="Y-m-d"', $html);
        $this->assertStringContainsString('data-config="enableTime=true"', $html);
        $this->assertStringContainsString('value="2025-01-01"', $html);
    }

    public function test_datepicker_component_renders_json_config_when_array_is_provided(): void
    {
        $html = Blade::render(
            '<x-mfw-inputable::datepicker name="start_date" :config="$config" />',
            ['config' => ['minDate' => 'today', 'enableTime' => true]]
        );

        $this->assertStringContainsString('data-config="{&quot;minDate&quot;:&quot;today&quot;,&quot;enableTime&quot;:true}"', $html);
    }

    public function test_input_date_mask_component_renders_placeholder_and_script_push(): void
    {
        $html = Blade::render(
            '<x-mfw-inputable::input-date-mask name="dob" />'
        );

        $this->assertStringContainsString('class="form-control inputdatemask', $html);
        $this->assertStringContainsString('placeholder="', $html);
    }

    public function test_validation_error_component_renders_message(): void
    {
        $errors = new ViewErrorBag;
        $errors->put('default', new MessageBag(['email' => ['Invalid email']]));
        View::share('errors', $errors);

        $html = Blade::render(
            '<x-mfw-inputable::validation-error field="email" />',
            ['errors' => $errors]
        );

        $this->assertStringContainsString('Invalid email', $html);
    }

    private function ensureGlobalTranslations(): void
    {
        $langFile = lang_path('en' . DIRECTORY_SEPARATOR . 'mfw-inputable-messages.php');

        if (File::exists($langFile)) {
            return;
        }

        File::ensureDirectoryExists(dirname($langFile));
        File::put($langFile, <<<'PHP'
<?php

return [
    'select_option' => 'Select',
    'select_date' => 'Choose a date...',
    'no_data_provided' => 'No data provided',
    'date_placeholder' => 'DD/MM/YYYY',
];
PHP);
    }
}
