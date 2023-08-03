---
title: Upgrading from v2.x
---

> If you see anything missing from this guide, please do not hesitate to [make a pull request](https://github.com/filamentphp/filament/edit/3.x/packages/forms/docs/10-upgrade-guide.md) to our repository! Any help is appreciated!

## New requirements

- Laravel v9.0+
- Livewire v3.0+

Please upgrade Filament before upgrading to Livewire v3. Instructions on how to upgrade Livewire can be found [here](https://livewire.laravel.com/docs/upgrading).

> **Livewire v3 is still in beta!**<br>
> Although breaking changes should be minimal, we recommend testing your application thoroughly before using Filament v3 in production.

## Upgrading automatically

Since Livewire v3 is still in beta, set the `minimum-stability` in your `composer.json` to `dev`:

```json
"minimum-stability": "dev",
```

The easiest way to upgrade your app is to run the automated upgrade script. This script will automatically upgrade your application to the latest version of Filament, and make changes to your code which handle most breaking changes.

```bash
composer require filament/upgrade:"^3.0-stable" -W --dev
vendor/bin/filament-v3
```

Make sure to carefully follow the instructions, and review the changes made by the script. You may need to make some manual changes to your code afterwards, but the script should handle most of the repetitive work for you.

Finally, you must run `php artisan filament:install` to finalize the Filament v3 installation. This command must be run for all new Filament projects.

You can now `composer remove filament/upgrade` as you don't need it any more.

> Some plugins you're using may not be available in v3 just yet. You could temporarily remove them from your `composer.json` file until they've been upgraded, replace them with a similar plugins that are v3-compatible, wait for the plugins to be upgraded before upgrading your app, or even write PRs to help the authors upgrade them.

## Upgrading manually

Since Livewire v3 is still in beta, set the `minimum-stability` in your `composer.json` to `dev`:

```json
"minimum-stability": "dev",
```

### High impact changes

#### Config file renamed and combined with other Filament packages

Only one config file is now used for all Filament packages. Most configuration has been moved into other parts of the codebase, and little remains. You should use the v3 documentation as a reference when replace the configuration options you did modify. To publish the new configuration file and remove the old one, run:

```bash
php artisan vendor:publish --tag=filament-config --force
rm config/forms.php
```

#### Panel configuration is now in each AppPanelProvider

By moving to a provider-per-panel system in V3, the old configuration method with `Filament::serving` for theme customisation, navigation groups, user menu items, etc in the AppServiceProvider has changed. Consult the [Configuration](https://filamentphp.com/docs/3.x/panels/configuration), [Navigation](https://filamentphp.com/docs/3.x/panels/navigation) and [Themes](https://filamentphp.com/docs/3.x/panels/themes) documentation for the new syntax.

#### `FORMS_FILESYSTEM_DRIVER` .env variable

The `FORMS_FILESYSTEM_DRIVER` .env variable has been renamed to `FILAMENT_FILESYSTEM_DISK`. This is to make it more consistent with Laravel, as Laravel 9 introduced this change as well. Please ensure that you update your .env files accordingly, and don't forget production!

#### New `@filamentScripts` and `@filamentStyles` Blade directives

The `@filamentScripts` and `@filamentStyles` Blade directives must be added to your Blade layout file/s. Since Livewire 3 no longer uses similar directives, you can replace `@livewireScripts` with `@filamentScripts`  and `@livewireStyles` with `@filamentStyles`.

#### CSS file removed

The CSS file for form components, `module.esm.css`, has been removed. Check `resources/css/app.css`. That CSS is now automatically loaded by `@filamentStyles`.

#### JavaScript files removed

You no longer need to import the `FormsAlpinePlugin` in your JavaScript files. Alpine plugins are now automatically loaded by `@filamentScripts`.

#### Heroicons have been updated to v2

The Heroicons library has been updated to v2. This means that any icons you use in your app may have changed names. You can find a list of changes [here](https://github.com/tailwindlabs/heroicons/releases/tag/v2.0.0).

### Medium impact changes

#### Date-time pickers

The date-time picker form field now uses the browser's native date picker by default. It usually has a better UX than the old date picker, but you may notice features missing, bad browser compatibility, or behavioural bugs. If you want to revert to the old date picker, you can use the `native(false)` method:

```php
use Filament\Forms\Components\DateTimePicker;

DateTimePicker::make('published_at')
    ->native(false)
```

#### Secondary color

Filament v2 had a `secondary` color for many components which was gray. All references to `secondary` should be replaced with `gray` to preserve the same appearance. This frees `secondary` to be registered to a new custom color of your choice.

#### `$get` and `$set` closure parameters

`$get` and `$set` parameters now use a type of either `\Filament\Forms\Get` or `\Filament\Forms\Set` instead of `\Closure`. This allows for better IDE autocomplete support of the parameters of each function.

An easy way to upgrade your code quickly is to find and replace:

- `Closure $get` to `\Filament\Forms\Get $get`
- `Closure $set` to `\Filament\Forms\Set $set`

#### TextInput numeric masks are replaced with Alpine.js masks

Filament v2 had a fluent closure mask syntax for managing more complex input masks. In v3 you can use Alpine.js masking instead. So instead of 
```
->mask(fn (TextInput\Mask $mask) => $mask
    ->numeric() // And any other transformations needed
)
```
You can do:
```
->mask(RawJs::make(<<<'JS'
    $input.startsWith('34') || $input.startsWith('37') ? '9999 999999 99999' : '9999 9999 9999 9999'
JS))
```
If you were using input masks for money related fields, Alpine already has [builtin helpers](https://alpinejs.dev/plugins/mask#money-inputs) for it:
```
->mask(RawJs::make(<<<'JS'
    $money($input, '.', ',', 2)
JS))
```

#### Action class namespace deprecations

Actions under the `Filament\Pages\Actions\` have been deprecated, but not yet removed. Please use `Filament\Actions\` for page actions and `Filament\Tables\Actions\` for Table and Bulk actions.

#### Action execution via function call no longer works

In v2, you were able to place Action logic in external methods and call them, like so:
```
 Action::make('import_data')
    ->action('importData')
```
This was never [officially supported](https://github.com/filamentphp/filament/issues/7324#issuecomment-1659312359), but due to changes in how actions are called with Livewire, it no longer works. It is recommended to place your logic in an Action closure. See the [Action documentation](https://filamentphp.com/docs/3.x/actions/overview) for more information.

If you absolutely need the action logic to be in a separate function, you can call it as a closure:
```
Action::make('import_data')
    ->action(Closure::fromCallable([$this, 'importData']))
```
