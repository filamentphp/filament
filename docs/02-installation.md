---
title: Installation
---
import RadioGroup from "@components/RadioGroup.astro"
import RadioGroupOption from "@components/RadioGroupOption.astro"
import Checkboxes from "@components/Checkboxes.astro"
import Checkbox from "@components/Checkbox.astro"

<div x-data="{ package: 'panels' }">

<RadioGroup model="package">
    <RadioGroupOption value="panels">
        Panels

        <span slot="description">
            Just the panel
        </span>
    </RadioGroupOption>

    <RadioGroupOption value="components">
        Components

        <span slot="description">
            Individual components
        </span>
    </RadioGroupOption>
</RadioGroup>

Let's test this: <span x-text="package"></span>

<div x-show="package === 'panels'">
## Panels

Panels are cool

```php
echo 'panels';
```
</div>

<div x-data="{ componentPackages: ['forms', 'infolists', 'tables', 'actions', 'notifications', 'widgets', 'support'] }" x-show="package === 'components'">
## Components

Components are cool

<Checkboxes>
    <Checkbox value="forms" model="componentPackages">
        Forms

        <span slot="description">
            Form builder
        </span>
    </Checkbox>

    <Checkbox value="infolists" model="componentPackages">
        Infolists

        <span slot="description">
            Infolist builder
        </span>
    </Checkbox>

    <Checkbox value="tables" model="componentPackages">
        Tables

        <span slot="description">
            Table builder
        </span>
    </Checkbox>

    <Checkbox value="actions" model="componentPackages">
        Actions

        <span slot="description">
            Actions
        </span>
    </Checkbox>

    <Checkbox value="notifications" model="componentPackages">
        Notifications

        <span slot="description">
            Notifications
        </span>
    </Checkbox>

    <Checkbox value="widgets" model="componentPackages">
        Widgets

        <span slot="description">
            Widgets
        </span>
    </Checkbox>

    <Checkbox value="support" model="componentPackages">
        UI

        <span slot="description">
            Blade components
        </span>
    </Checkbox>
</Checkboxes>

<span x-text="JSON.stringify(componentPackages)"></span>

```php
echo 'components';
```
</div>

</div>
