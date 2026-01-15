# MetaFramework Inputable

A Laravel Blade component library for building forms with Bootstrap 5 styling. Part of the MetaFramework ecosystem.

## Requirements

- PHP ^8.3
- Laravel ^11.0 | ^12.0
- Bootstrap 5

## Installation

### Via Composer

```bash
composer require aboleon/metaframework-inputable
```

The package auto-registers via Laravel's package discovery.

### Publish Assets

```bash
# Publish all (config, translations, assets)
php artisan mfw-inputable:install

# Or force overwrite existing files
php artisan mfw-inputable:install --force
```

Alternative manual publishing:

```bash
php artisan vendor:publish --tag=mfw-inputable-config
php artisan vendor:publish --tag=mfw-inputable-translations
php artisan vendor:publish --tag=mfw-inputable-assets
```

## Configuration

After publishing, configure in `config/mfw-inputable.php`:

```php
return [
    'google' => [
        // Required for GooglePlaces component
        'places_api_key' => env('MFW_INPUTABLE_GOOGLE_PLACES_KEY', ''),
    ],
    // Optional: Custom country resolver class
    'countries_resolver' => null,
];
```

## Available Components

| Component | Description |
|-----------|-------------|
| `input` | Text input with prefix/suffix support |
| `number` | Numeric input with min/max/step |
| `textarea` | Textarea with optional TinyMCE |
| `select` | Dropdown with optional groups |
| `checkbox` | Single checkbox or switch |
| `radio` | Radio button group |
| `datepicker` | Date picker (Flatpickr) |
| `input-date-mask` | Date input with mask |
| `google-places` | Address autocomplete |
| `validation-error` | Field error display |

## Usage

All components use the `mfw-inputable` namespace:

```blade
<x-mfw-inputable::component-name ... />
```

### Input

Basic text input with label and validation.

```blade
<x-mfw-inputable::input
    name="email"
    type="email"
    label="Email Address"
    :value="old('email', $user->email)"
    required
/>
```

**Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `name` | string | required | Field name (supports dot notation) |
| `value` | mixed | `''` | Field value |
| `type` | string | `'text'` | HTML input type |
| `label` | string | `null` | Field label |
| `class` | string | `''` | CSS classes |
| `required` | bool | `false` | Required field |
| `readonly` | bool | `false` | Read-only field |
| `prefix` | string | `null` | Input group prefix |
| `suffix` | string | `null` | Input group suffix |
| `params` | array | `[]` | Extra HTML attributes |

### Number

Numeric input with constraints.

```blade
<x-mfw-inputable::number
    name="price"
    label="Price"
    :value="$product->price"
    :min="0"
    :max="99999"
    :step="0.01"
    prefix="€"
    required
/>
```

**Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `name` | string | required | Field name |
| `value` | numeric | `null` | Field value |
| `min` | int | `0` | Minimum value |
| `max` | int | `null` | Maximum value |
| `step` | mixed | `'any'` | Step increment |
| `prefix` | string | `''` | Input group prefix |
| `suffix` | string | `''` | Input group suffix |

### Textarea

Textarea with optional TinyMCE rich text editor.

```blade
{{-- Plain textarea --}}
<x-mfw-inputable::textarea
    name="notes"
    label="Notes"
    :value="old('notes')"
    :height="150"
/>

{{-- With TinyMCE (simplified toolbar) --}}
<x-mfw-inputable::textarea
    name="description"
    label="Description"
    class="simplified"
    :value="$product->description"
/>

{{-- With TinyMCE (full toolbar) --}}
<x-mfw-inputable::textarea
    name="content"
    label="Content"
    class="extended"
    :height="400"
    :value="$article->content"
/>
```

**Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `name` | string | required | Field name |
| `value` | string | `null` | Content |
| `label` | string | `null` | Field label |
| `class` | string | `''` | CSS classes (`simplified` or `extended` for TinyMCE) |
| `height` | int | `200` | Height in pixels |
| `required` | bool | `false` | Required field |
| `readonly` | bool | `false` | Read-only field |

### Select

Dropdown select with optional grouped options.

```blade
{{-- Simple select --}}
<x-mfw-inputable::select
    name="status"
    label="Status"
    :values="['draft' => 'Draft', 'published' => 'Published']"
    :affected="old('status', $post->status)"
/>

{{-- With grouped options --}}
<x-mfw-inputable::select
    name="category_id"
    label="Category"
    :values="$groupedCategories"
    :affected="old('category_id')"
    :group="true"
/>
```

**Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `name` | string | required | Field name |
| `values` | array | required | Options `[value => label]` |
| `affected` | mixed | `null` | Selected value |
| `label` | string | `''` | Field label |
| `nullable` | bool | `true` | Show empty option |
| `defaultselecttext` | string | `''` | Empty option text |
| `group` | bool | `false` | Enable option groups |
| `class` | string | `null` | CSS classes |

### Checkbox

Single checkbox or Bootstrap switch.

```blade
{{-- Standard checkbox --}}
<x-mfw-inputable::checkbox
    name="is_active"
    label="Active"
    :value="1"
    :affected="old('is_active', $user->is_active)"
/>

{{-- Switch style --}}
<x-mfw-inputable::checkbox
    name="notifications"
    label="Enable notifications"
    :switch="true"
    :affected="$settings->notifications"
/>
```

**Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `name` | string | required | Field name |
| `value` | mixed | `1` | Checkbox value |
| `affected` | mixed | `null` | Current value (bool, Collection, or array) |
| `label` | string | `null` | Checkbox label |
| `switch` | bool | `false` | Use Bootstrap switch styling |
| `class` | string | `''` | CSS classes |

### Radio

Radio button group.

```blade
<x-mfw-inputable::radio
    name="gender"
    label="Gender"
    :values="['male' => 'Male', 'female' => 'Female', 'other' => 'Other']"
    :affected="old('gender', $user->gender)"
    :default="'other'"
/>
```

**Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `name` | string | required | Field name |
| `values` | array | required | Options `[value => label]` |
| `affected` | mixed | required | Selected value |
| `label` | string | `null` | Group label |
| `default` | mixed | `null` | Default selection |
| `class` | string | `'my-3 p-0'` | Wrapper CSS classes |

### Datepicker

Date picker using Flatpickr library.

```blade
<x-mfw-inputable::datepicker
    name="birth_date"
    label="Birth Date"
    :value="old('birth_date', $user->birth_date?->format('d/m/Y'))"
    format="d/m/Y"
    required
/>

{{-- With time picker --}}
<x-mfw-inputable::datepicker
    name="appointment"
    label="Appointment"
    :value="$appointment->datetime"
    config="enableTime=true,dateFormat=d/m/Y H:i"
/>
```

**Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `name` | string | required | Field name |
| `value` | string | `null` | Initial date |
| `label` | string | `null` | Field label |
| `format` | string | `'d/m/Y'` | Date format |
| `config` | string | `null` | Flatpickr config string |
| `required` | bool | `false` | Required field |

### Google Places

Address autocomplete with geolocation using Google Places API.

```blade
<x-mfw-inputable::google-places
    :geo="$address"
    field="address"
    label="Address"
    :params="['required' => ['route', 'postal_code', 'locality']]"
    :hidden="['administrative_area_level_2']"
/>
```

**Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `geo` | GooglePlacesInterface | required | Geo data object |
| `field` | string | `'mfw_geo'` | Form field prefix |
| `label` | string | `null` | Component label |
| `placeholder` | string | `''` | Search input placeholder |
| `notice` | string | `null` | Help text |
| `params` | array | `[]` | Options (e.g., `['required' => [...]]`) |
| `hidden` | array | `[...]` | Fields to hide |

**GooglePlacesInterface:**

Your geo model should implement `GooglePlacesInterface` with these properties:

```php
use MetaFramework\Inputable\Contracts\GooglePlacesInterface;

class Address implements GooglePlacesInterface
{
    public ?string $text_address;
    public ?string $street_number;
    public ?string $route;
    public ?string $postal_code;
    public ?string $locality;
    public ?string $administrative_area_level_1;
    public ?string $administrative_area_level_2;
    public ?string $country;
    public ?string $country_code;
    public ?float $lat;
    public ?float $lon;
}
```

### Validation Error

Display validation error for a field.

```blade
<x-mfw-inputable::validation-error field="email" />
```

## Complete Examples

### Example 1: User Registration Form

```blade
<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="row">
        <div class="col-md-6">
            <x-mfw-inputable::input
                name="first_name"
                label="First Name"
                :value="old('first_name')"
                required
            />
        </div>
        <div class="col-md-6">
            <x-mfw-inputable::input
                name="last_name"
                label="Last Name"
                :value="old('last_name')"
                required
            />
        </div>
    </div>

    <x-mfw-inputable::input
        name="email"
        type="email"
        label="Email Address"
        :value="old('email')"
        required
    />

    <x-mfw-inputable::input
        name="password"
        type="password"
        label="Password"
        required
    />

    <x-mfw-inputable::datepicker
        name="birth_date"
        label="Date of Birth"
        :value="old('birth_date')"
        format="d/m/Y"
    />

    <x-mfw-inputable::select
        name="country"
        label="Country"
        :values="$countries"
        :affected="old('country')"
        required
    />

    <x-mfw-inputable::radio
        name="gender"
        label="Gender"
        :values="['male' => 'Male', 'female' => 'Female', 'other' => 'Prefer not to say']"
        :affected="old('gender')"
    />

    <x-mfw-inputable::checkbox
        name="terms"
        label="I accept the terms and conditions"
        :affected="old('terms')"
        required
    />

    <x-mfw-inputable::checkbox
        name="newsletter"
        label="Subscribe to newsletter"
        :switch="true"
        :affected="old('newsletter', true)"
    />

    <button type="submit" class="btn btn-primary">Register</button>
</form>
```

### Example 2: Product Edit Form with Address

```blade
<form method="POST" action="{{ route('products.update', $product) }}">
    @csrf
    @method('PUT')

    <x-mfw-inputable::input
        name="name"
        label="Product Name"
        :value="old('name', $product->name)"
        required
    />

    <x-mfw-inputable::textarea
        name="description"
        label="Description"
        class="extended"
        :height="300"
        :value="old('description', $product->description)"
    />

    <div class="row">
        <div class="col-md-4">
            <x-mfw-inputable::number
                name="price"
                label="Price"
                :value="old('price', $product->price)"
                :min="0"
                :step="0.01"
                prefix="€"
                required
            />
        </div>
        <div class="col-md-4">
            <x-mfw-inputable::number
                name="stock"
                label="Stock Quantity"
                :value="old('stock', $product->stock)"
                :min="0"
                :step="1"
            />
        </div>
        <div class="col-md-4">
            <x-mfw-inputable::number
                name="weight"
                label="Weight"
                :value="old('weight', $product->weight)"
                :min="0"
                :step="0.001"
                suffix="kg"
            />
        </div>
    </div>

    <x-mfw-inputable::select
        name="category_id"
        label="Category"
        :values="$categories"
        :affected="old('category_id', $product->category_id)"
        :group="true"
        required
    />

    <x-mfw-inputable::checkbox
        name="is_featured"
        label="Featured product"
        :switch="true"
        :affected="old('is_featured', $product->is_featured)"
    />

    <hr class="my-4">
    <h5>Warehouse Address</h5>

    <x-mfw-inputable::google-places
        :geo="$product->warehouse ?? new \App\Models\Address()"
        field="warehouse"
        label="Search address"
        :params="[
            'required' => ['route', 'postal_code', 'locality', 'country'],
            'componentRestrictions' => ['country' => ['fr', 'be', 'ch']]
        ]"
        :hidden="['administrative_area_level_1_short', 'administrative_area_level_2']"
    />

    <button type="submit" class="btn btn-primary">Save Product</button>
</form>
```

## Translations

The package includes translations for:
- English (en)
- French (fr)
- Bulgarian (bg)

Translation keys are in `mfw-inputable-messages` and `mfw-inputable-geo` namespaces.

## Assets

Published to `public/vendor/mfw-inputable/`:

- **Flatpickr** - Date picker library with themes
- **TinyMCE** - Rich text editor
- **Google Places** - Address autocomplete script
- **Input Date Mask** - Date masking script

## License

MIT License - Open Source
