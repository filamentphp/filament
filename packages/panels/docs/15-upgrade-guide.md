---
title: Upgrading from v4.x
---

> If you see anything missing from this guide, please do not hesitate to [make a pull request](https://github.com/filamentphp/filament/edit/4.x/packages/panels/docs/14-upgrade-guide.md) to our repository! Any help is appreciated!

## New requirements

- Tailwind CSS v4.0+

## Upgrading automatically

The easiest way to upgrade your app is to run the automated upgrade script. This script will automatically upgrade your application to the latest version of Filament and make changes to your code, which handles most breaking changes.

```bash
composer require filament/upgrade:"^4.0" -W --dev

vendor/bin/filament-v4
```

Make sure to carefully follow the instructions, and review the changes made by the script. You may need to make some manual changes to your code afterwards, but the script should handle most of the repetitive work for you.

Finally, you must run `php artisan filament:install` to finalize the Filament v4 installation. This command must be run for all new Filament projects.

You can now `composer remove filament/upgrade` as you don't need it anymore.

> Some plugins you're using may not be available in v4 just yet. You could temporarily remove them from your `composer.json` file until they've been upgraded, replace them with a similar plugins that are v4-compatible, wait for the plugins to be upgraded before upgrading your app, or even write PRs to help the authors upgrade them.

## Upgrading manually

After upgrading the dependency via Composer, you should execute `php artisan filament:upgrade` in order to clear any Laravel caches and publish the new frontend assets.

### High-impact changes

#### The `FILAMENT_FILESYSTEM_DISK` environment variable

If you hadn't published the Filament configuration file to `config/filament.php`, Filament will now reference the `FILESYSTEM_DISK` environment variable instead of `FILAMENT_FILESYSTEM_DISK` when uploading files. Laravel also uses this environment variable to represent the default filesystem disk, so this change aims to bring Filament in line to avoid potential confusion.

If you have published the Filament configuration file, and you have a `default_filesystem_disk` key set, you can ignore this change as your app will continue to use the old configuration value.

If you have not published the Filament configuration file, or you have removed the `default_filesystem_disk` key from it:

- If you do not have a `FILAMENT_FILESYSTEM_DISK` environment variable set:
  - If the `FILESYSTEM_DISK` environment variable is set to `public`, you can ignore this change, since `public` was the default value for `FILAMENT_FILESYSTEM_DISK` before.
  - If the `FILESYSTEM_DISK` environment variable is set to something else, you can ignore this change if you are happy with the `FILESYSTEM_DISK` value being used to upload files in Filament. Please be aware that not using a `public` or `s3` disk may lead to unexpected behaviour, as the `local` disk stores private files but is unable to generate public URLs for previews or downloads.
- If you do have a `FILAMENT_FILESYSTEM_DISK` environment variable set:
  - If it is the same as `FILESYSTEM_DISK`, you can remove it completely.
  - If it is different from `FILESYSTEM_DISK`, and it should be, you can [publish the Filament configuration file](installation#publishing-configuration) and set the `default_filesystem_disk` to reference the old `FILAMENT_FILESYSTEM_DISK` environment variable like it was before.

### Medium-impact changes

#### The `$maxContentWidth` property on page classes

The `$maxContentWidth` property on page classes has a new type. It is now able to accept `MaxWidth` enum values, as well as strings and null:

```php
use Filament\Support\Enums\MaxWidth;

protected MaxWidth | string | null $maxContentWidth = null;
```

### Low-impact changes
