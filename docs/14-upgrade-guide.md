---
title: Upgrade guide
---
import Aside from "@components/Aside.astro"

<Aside variant="info">
    If you see anything missing from this guide, please don’t hesitate to [make a pull request](https://github.com/filamentphp/filament/edit/5.x/docs/14-upgrade-guide.md) to our repository! Any help is appreciated!
</Aside>

## New requirements

- PHP 8.2+
- Laravel v11.28+
- Livewire v4.0+
- Tailwind CSS v4.0+

## Running the automated upgrade script

<Aside variant="info">
    Some plugins you're using may not be available in v5 just yet. You could temporarily remove them from your `composer.json` file until they've been upgraded, replace them with a similar plugins that are v5-compatible, wait for the plugins to be upgraded before upgrading your app, or even write PRs to help the authors upgrade them.
</Aside>

You can upgrade your Filament app by running the automated upgrade script. This script will check your codebase for compatibility issues, and suggest Composer update commands to run:

```bash
composer require filament/upgrade:"^5.0" -W --dev

vendor/bin/filament-v5

# Run the commands output by the upgrade script, they are unique to your app
composer require filament/filament:"^5.0" -W --no-update
composer update
```

<Aside variant="warning">
    When using Windows PowerShell to install Filament, you may need to run the command below, since it ignores `^` characters in version constraints:

    ```bash
    composer require filament/upgrade:"~5.0" -W --dev

    vendor/bin/filament-v5

    # Run the commands output by the upgrade script, they are unique to your app
    composer require filament/filament:"~5.0" -W --no-update
    composer update
    ```
</Aside>

You can now `composer remove filament/upgrade --dev` as you don't need it anymore.
