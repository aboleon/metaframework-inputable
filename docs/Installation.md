# Metaframework Inputable Installation

## Publish package resources

After installing the package (either directly or via Metaframework) copy the configuration, translations (into `lang/<locale>`), and front-end assets into your application with the dedicated install command:

```bash
php artisan mfw-inputable:install
```

Re-run the command with `--force` to overwrite existing files if you need to refresh them.

You can still use Laravel's vendor publish commands when you want to customise individual publish groups:

```bash
php artisan vendor:publish --provider="MetaFramework\Inputable\InputableServiceProvider" --tag=mfw-inputable-config
php artisan vendor:publish --provider="MetaFramework\Inputable\InputableServiceProvider" --tag=mfw-inputable-translations
php artisan vendor:publish --provider="MetaFramework\Inputable\InputableServiceProvider" --tag=mfw-inputable-assets
# publish everything in a single step
php artisan vendor:publish --provider="MetaFramework\Inputable\InputableServiceProvider"
```
