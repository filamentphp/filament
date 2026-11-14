---
title: Deploying to production
---

## Introduction

Deploying a Laravel app using Filament to production is similar to deploying any other Laravel app. However, there are a few additional steps you should take to ensure that your Filament panel is optimized for performance and security.

For tips focused on local development performance, see [Optimizing local development](introduction/optimizing-local-development).

## Ensure that users are authorized to access panels

When Filament detects that your app's `APP_ENV` is not `local`, it will require you to set up authorization for your users. This is to ensure that only authorized users can access your Filament panel in production, while keeping the local environment easy to get started with.

To authorize users to access a panel, you should follow the [guide in the users section](users/overview#authorizing-access-to-the-panel).

> [!WARNING]
> If you do not follow these steps and your user model does not implement the `FilamentUser` interface, no users will be able to sign in to your panel in production.

## Improving Filament panel performance

### Optimizing Filament for production

To optimize Filament for production, you should run the following command in your deployment script:

```bash
php artisan filament:optimize
```

This command will [cache the Filament components](#caching-filament-components) and additionally the [Blade Icons](#caching-blade-icons), which can significantly improve the performance of your Filament panels. This command is a shorthand for the commands `php artisan filament:cache-components` and `php artisan icons:cache`.

To clear the caches at once, you can run:

```bash
php artisan filament:optimize-clear
```

#### Caching Filament components

If you're not using the [`filament:optimize` command](#optimizing-filament-for-production), you may wish to consider running `php artisan filament:cache-components` in your deployment script, especially if you have large numbers of components (resources, pages, widgets, relation managers, custom Livewire components, etc.). This will create cache files in the `bootstrap/cache/filament` directory of your application, which contain indexes for each type of component. This can significantly improve the performance of Filament in some apps, as it reduces the number of files that need to be scanned and auto-discovered for components.

However, if you are actively developing your app locally, you should avoid using this command, as it will prevent any new components from being discovered until the cache is cleared or rebuilt.

You can clear the cache at any time without rebuilding it by running `php artisan filament:clear-cached-components`.

#### Caching Blade Icons

If you're not using the [`filament:optimize` command](#optimizing-filament-for-production), you may wish to consider running `php artisan icons:cache` locally, and also in your deployment script. This is because Filament uses the [Blade Icons](https://blade-ui-kit.com/blade-icons) package, which can be much more performant when cached.

### Enabling OPcache on your server

To check if [OPcache](https://www.php.net/manual/en/book.opcache.php) is enabled, run:

```bash
php -r "echo 'opcache.enable => ' . ini_get('opcache.enable') . PHP_EOL;"
```

You should see `opcache.enable => 1`. If not, enable it by adding the following line to your `php.ini`:

```ini
opcache.enable=1 # Enable OPcache
```

From the [Laravel Forge documentation](https://laravel.com/forge/docs/servers/php#opcache):

> [!TIP]
> Optimizing the PHP OPcache for production will configure OPcache to store your compiled PHP code in memory to greatly improve performance.

Please use a search engine to find the relevant OPcache setup instructions for your environment.

### Optimizing your Laravel app

You should also consider optimizing your Laravel app for production by running `php artisan optimize` in your deployment script. This will cache the configuration files and routes.

## Ensuring assets are up to date

During the Filament installation process, Filament adds the `php artisan filament:upgrade` command to the `composer.json` file, in the `post-autoload-dump` script. This command will ensure that your assets are up to date whenever you download the package.

We strongly suggest that this script remains in your `composer.json` file, otherwise you may run into issues with missing or outdated assets in your production environment. However, if you must remove it, make sure that the command is run manually in your deployment process.

## Expiring outdated pages after a deployment

Users may leave Filament pages open during a deployment. Livewire sends the component state from those pages to your new code on their next interaction, without running `mount()` again. If a deployment changes the structure or meaning of that state, the old page may no longer work correctly. For example, a new public property initialized in `mount()` will not receive its intended initial value, or an existing form value may be interpreted in a different format.

When deploying an incompatible change, update Livewire's `release_token` in `config/livewire.php` to a new, stable value:

```php
'release_token' => 'b',
```

If you have not published this configuration file, you can do so using `php artisan livewire:publish --config`. If your application caches configuration, rebuild it with `php artisan config:cache` during deployment.

Changing the token expires existing Livewire component snapshots throughout your application, including Filament pages and widgets. On their next Livewire request, outdated components return HTTP `419` without running their requested actions. By default, Filament leaves this response to Livewire, which asks the user whether they want to refresh the page.

> [!WARNING]
> Refreshing discards unsaved changes. Keep the token unchanged for compatible deployments to avoid unnecessarily interrupting users. Use the same token on every application server, and never generate it per request using a timestamp or random value. During rolling deployments, requests reaching servers with different tokens can cause page expiry prompts until the deployment finishes.

Review state compatibility when upgrading Filament or plugins as well as when changing your own components. Livewire's own release token does not track Filament or plugin versions, and Filament does not automatically change your application's token on an upgrade.

If a change only affects one Livewire component, you can override its `public static function releaseToken(): string` method instead. This also works on Filament page and widget classes. Return a stable string and change it only when that component's existing state becomes incompatible. If you already override this method, preserve any compatibility token supplied by a parent implementation.

> [!WARNING]
> A request containing multiple component updates fails if any component's token is outdated. These updates are processed sequentially, so actions on earlier, compatible components may already have run before an outdated component causes the request to fail.
