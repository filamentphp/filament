---
title: Upgrade guide
---
import Aside from "@components/Aside.astro"

<Aside variant="info">
    If you see anything missing from this guide, please don’t hesitate to [make a pull request](https://github.com/filamentphp/filament/edit/6.x/docs/14-upgrade-guide.md) to our repository! Any help is appreciated!
</Aside>

## Running the automated upgrade script

<Aside variant="info">
    Some plugins you're using may not be available in v6 just yet. You could temporarily remove them from your `composer.json` file until they've been upgraded, replace them with a similar plugins that are v6-compatible, wait for the plugins to be upgraded before upgrading your app, or even write PRs to help the authors upgrade them.
</Aside>

The first step to upgrade your Filament app is to run the automated upgrade script. This script will automatically upgrade your application to the latest version of Filament and make changes to your code, which handles breaking changes:

```bash
composer require filament/upgrade:"^6.0" -W --dev

vendor/bin/filament-v6

# Run the commands output by the upgrade script, they are unique to your app
composer require filament/filament:"^6.0" -W --no-update
composer update
```

<Aside variant="warning">
    When using Windows PowerShell to install Filament, you may need to run the command below, since it ignores `^` characters in version constraints:

    ```bash
    composer require filament/upgrade:"~6.0" -W --dev

    vendor/bin/filament-v6

    # Run the commands output by the upgrade script, they are unique to your app
    composer require filament/filament:"~6.0" -W --no-update
    composer update
    ```
</Aside>

Make sure to carefully follow the instructions, and review the changes made by the script. You may need to make some manual changes to your code afterwards, but the script should handle most of the repetitive work for you.

You can now `composer remove filament/upgrade --dev` as you don't need it anymore.

## Breaking changes that must be handled manually

<div x-data="{ packages: ['panels', 'forms', 'infolists', 'tables', 'actions', 'notifications', 'widgets', 'support'] }">

To begin, filter the upgrade guide for your specific needs by selecting only the packages that you use in your project:

<Checkboxes>
    <Checkbox value="panels" model="packages">
        Panels
    </Checkbox>

    <Checkbox value="forms" model="packages">
        Forms

        <span slot="description">
            This package is also often used in a panel, or using the tables or actions package.
        </span>
    </Checkbox>

    <Checkbox value="infolists" model="packages">
        Infolists

        <span slot="description">
            This package is also often used in a panel, or using the tables or actions package.
        </span>
    </Checkbox>

    <Checkbox value="tables" model="packages">
        Tables

        <span slot="description">
            This package is also often used in a panel.
        </span>
    </Checkbox>

    <Checkbox value="actions" model="packages">
        Actions

        <span slot="description">
            This package is also often used in a panel.
        </span>
    </Checkbox>

    <Checkbox value="notifications" model="packages">
        Notifications

        <span slot="description">
            This package is also often used in a panel.
        </span>
    </Checkbox>

    <Checkbox value="widgets" model="packages">
        Widgets

        <span slot="description">
            This package is also often used in a panel.
        </span>
    </Checkbox>

    <Checkbox value="support" model="packages">
        Blade UI components
    </Checkbox>
</Checkboxes>

### Low-impact changes

<Disclosure open x-show="packages.includes('support')">
<span slot="summary">The modal content is now wrapped in a `fi-modal-window-scroll` element when using a sticky header or footer</span>

To fix an issue where modals with a sticky header or footer could overflow the viewport, the internal DOM structure of the modal component has changed. When a modal has a sticky header (`stickyHeader()`) or a sticky footer (`stickyFooter()`), and is not a slide-over or a screen-width modal, its heading, content, and footer are now wrapped in a new scrollable `fi-modal-window-scroll` element:

```html
<div class="fi-modal-window">
    <div class="fi-modal-window-scroll">
        <div class="fi-modal-header"><!-- ... --></div>
        <div class="fi-modal-content"><!-- ... --></div>
        <div class="fi-modal-footer"><!-- ... --></div>
    </div>
</div>
```

If you have written custom CSS or a theme that targets `fi-modal-header`, `fi-modal-content`, or `fi-modal-footer` as direct children of `fi-modal-window`, you should update your selectors to account for the new `fi-modal-window-scroll` wrapper.
</Disclosure>

</div>
