---
title: Contributing
contents: false
---
import Aside from "@components/Aside.astro"

<Aside variant="info">
    Parts of this guide are adapted from [Laravel's contribution guide](https://laravel.com/docs/contributions), which served as valuable inspiration.
</Aside>

## Reporting bugs

If you discover a bug in Filament, please report it by opening an issue on our [GitHub repository](https://github.com/filamentphp/filament/issues/new/choose). Before opening an issue, search through the [existing issues](https://github.com/filamentphp/filament/issues?q=is%3Aissue) to check if the bug has already been reported.

Please include as much information as possible, particularly the version numbers of packages in your application. You can use this Artisan command in your application to open a new issue with all the correct versions automatically pre-filled:

```bash
php artisan make:filament-issue
```

When creating an issue, we require a "reproduction repository". **Please do not link to your actual project**. Instead, what we need is a _minimal_ reproduction in a fresh project without any unnecessary code. This means it doesn't matter if your real project is private/confidential since we want a link to a separate, isolated reproduction. This allows us to fix the problem much quicker. **Issues will be automatically closed and not reviewed if this is missing, to preserve maintainer time and ensure the process is fair for those who put effort into reporting.** If you believe a reproduction repository is not suitable for the issue, which is a very rare case, please `@danharrin` and explain why. Saying "it's just a simple issue" is not an excuse for not creating a repository! [Need a head start? We have a template Filament project for you.](https://unitedbycode.com/filament-issue)

Remember, bug reports are created in the hope that others with the same problem will be able to collaborate with you on solving it. Do not expect that the bug report will automatically receive attention or that others will jump to fix it. Creating a bug report serves to help yourself and others start on the path of fixing the problem.

## Development of new features

If you would like to propose a new feature or improvement to Filament, you may use our [discussion forum](https://github.com/filamentphp/filament/discussions) hosted on GitHub. If you intend to implement the feature yourself in a pull request, we advise you to `@danharrin` (a core maintainer of Filament) in your feature discussion beforehand and ask if it is suitable for the framework to prevent wasting your time.

## Development of plugins

If you would like to develop a plugin for Filament, please refer to the [plugin development section](../plugins) in the documentation. Our [Discord](https://filamentphp.com/discord) server is also a great place to ask questions and get help with plugin development. You can start a conversation in the [`#plugin-developers-chat`](https://discord.com/channels/883083792112300104/970354547723730955) channel.

You can also [submit your plugin to be advertised on the Filament website](https://github.com/filamentphp/filamentphp.com/blob/main/README.md#contributing).

## Developing with a local copy of Filament

If you want to contribute to the Filament packages, then you may want to test it in a real Laravel project:

- Fork [the GitHub repository](https://github.com/filamentphp/filament) to your GitHub account.
- Create a Laravel app locally.
- Clone your fork into your Laravel app's root directory.
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

Once you've finished making changes, you can commit them and submit a pull request to [the GitHub repository](https://github.com/filamentphp/filament).

## Checking for outdated translations

To check for outdated translations, you can use our Translation Tool. Clone the Filament repository, install the dependencies for the commands and then run the command.

```bash
# Clone
git clone git@github.com:filamentphp/filament.git

# Install dependencies
composer install

# Run the tool  
./bin/translation-tool.php
```

First select "List outdated translations" as the command and then choose the locale you want to check. This command will show you which translations are missing for the specified locale. You can then submit a pull request with the missing translations to [the GitHub repository](https://github.com/filamentphp/filament).

## Security vulnerabilities

If you discover a security vulnerability within Filament, please [report it through GitHub](https://github.com/filamentphp/filament/security/advisories). All security vulnerabilities will be promptly addressed. Please see our [version support policy](version-support-policy) to understand which versions are currently under maintenance.

## Code of Conduct

Please note that Filament is released with a [Contributor Code of Conduct](https://github.com/filamentphp/filament/blob/4.x/CODE_OF_CONDUCT.md). By participating in this project, you agree to abide by its terms.
