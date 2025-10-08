# Metaframework Inputable Installation

## Publish package resources

After installing the package (either directly or via Metaframework) you should publish the configuration, translations, and front-end assets:

```bash
php artisan vendor:publish --provider="MetaFramework\Inputable\InputableServiceProvider" --tag=mfw-input-config
php artisan vendor:publish --provider="MetaFramework\Inputable\InputableServiceProvider" --tag=mfw-input-translations
php artisan vendor:publish --provider="MetaFramework\Inputable\InputableServiceProvider" --tag=mfw-input-assets
# publish everything in a single step
php artisan vendor:publish --provider="MetaFramework\Inputable\InputableServiceProvider"
```

The package automatically Automatically copies its publishable resources (configuration, translations, assets) into `config/mfw-input.php`, `lang/vendor/mfw-input`, and `public/vendor/mfw-input` the first time it boots (existing files are left untouched). Use the publish commands above if you need to refresh or relocate them later. The publish commands above remain available if you need to force a refresh or customise the output path.
