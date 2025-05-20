---
title: Contributing
---

> Parts of this guide are taken from [Laravel's contribution guide](https://laravel.com/docs/contributions), and it served as very useful inspiration.

## Reporting bugs

If you find a bug in Filament, please report it by opening an issue on our [GitHub repository](https://github.com/filamentphp/filament/issues/new/choose). Before opening an issue, please search the [existing issues](https://github.com/filamentphp/filament/issues?q=is%3Aissue) to see if the bug has already been reported.

Please make sure to include as much information as possible, including the version of packages in your app. You can use this Artisan command in your app to open a new issue with all the correct versions pre-filled:

```bash
php artisan make:filament-issue
```

When creating an issue, we require a "reproduction repository". **Please do not link to your actual project**, what we need instead is a _minimal_ reproduction in a fresh project without any unnecessary code. This means it doesn't matter if your real project is private / confidential, since we want a link to a separate, isolated reproduction. This allows us to fix the problem much quicker. **Issues will be automatically closed and not reviewed if this is missing, to preserve maintainer time and to ensure the process is fair for those who put effort into reporting.** If you believe a reproduction repository is not suitable for the issue, which is a very rare case, please `@danharrin` and explain why. Saying that "it's just a simple issue" is not an excuse for not creating a repository! [Need a headstart? We have a template Filament project for you.](https://unitedbycode.com/filament-issue)

Remember, bug reports are created in the hope that others with the same problem will be able to collaborate with you on solving it. Do not expect that the bug report will automatically see any activity or that others will jump to fix it. Creating a bug report serves to help yourself and others start on the path of fixing the problem.

## Development of new features

If you would like to propose a new feature or improvement to Filament, you may use our [discussion form](https://github.com/filamentphp/filament/discussions) hosted on GitHub. If you are intending on implementing the feature yourself in a pull request, we advise you to `@danharrin` in your feature discussion beforehand and ask if it is suitable for the framework to prevent wasting your time.

## Development of plugins

If you would like to develop a plugin for Filament, please refer to the [plugin development section](https://filamentphp.com/docs/support/plugins) here in the documentation. Our [Discord](https://filamentphp.com/discord) server is also a great place to ask questions and get help with plugin development. You can start a conversation in the [`#plugin-developers-chat`](https://discord.com/channels/883083792112300104/970354547723730955) channel.

You can [submit your plugin to the Filament website](https://github.com/filamentphp/filamentphp.com/blob/main/README.md#contributing).

## Developing with a local copy of Filament

If you want to contribute to the Filament packages, then you may want to test it in a real Laravel project:

- Fork [the GitHub repository](https://github.com/filamentphp/filament) to your GitHub account.
- Create a Laravel app locally.
- Clone your fork in your Laravel app's root directory.
- In the `/filament` directory, create a branch for your fix, e.g. `fix/error-message`.

Install the packages in your app's `composer.json`:

```jsonc
{
    // ...
    "require": {
        "filament/filament": "*",
    },
    "minimum-stability": "dev",
    "repositories": [
        {
            "type": "path",
            "url": "filament/packages/*"
        }
    ],
    // ...
}
```

Now, run `composer update`.

Once you're finished making changes, you can commit them and submit a pull request to [the GitHub repository](https://github.com/filamentphp/filament).

## Checking for missing translations

Set up a Laravel app, and install the [panel builder](https://filamentphp.com/docs/admin/installation).

Now, if you want to check for missing Spanish translations, run:

```bash
php artisan filament:check-translations es
```

This will let you know which translations are missing for this locale. You can make a pull request with the changes to [the GitHub repository](https://github.com/filamentphp/filament).

If you've published the translations into your app and you'd like to check those instead, try:

```bash
php artisan filament:check-translations es --source=app
```

## Security vulnerabilities

If you discover a security vulnerability within Filament, please email Dan Harrin via [dan@danharrin.com](mailto:dan@danharrin.com). All security vulnerabilities will be promptly addressed.

## Code of Conduct

Please note that Filament is released with a [Contributor Code of Conduct](https://github.com/filamentphp/filament/blob/afa0c703da18ce78b508951436f571c9d4813db6/CODE_OF_CONDUCT.md). By participating in this project you agree to abide by its terms.
