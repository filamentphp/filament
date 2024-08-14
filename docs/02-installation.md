---
title: Installation
---
import RadioGroup from "@components/RadioGroup.astro"
import RadioGroupOption from "@components/RadioGroupOption.astro"
import RadioGroupOptionLabel from "@components/RadioGroupOptionLabel.astro"
import RadioGroupOptionDescription from "@components/RadioGroupOptionDescription.astro"

<div x-data="{ package: 'panels' }">

<RadioGroup x-model="package">
    <RadioGroupOption value="panels">
        <RadioGroupOptionLabel>
            Panels
        </RadioGroupOptionLabel>

        <RadioGroupOptionDescription>
            Just the panel
        </RadioGroupOptionDescription>
    </RadioGroupOption>

    <RadioGroupOption value="components">
        <RadioGroupOptionLabel>
            Components
        </RadioGroupOptionLabel>

        <RadioGroupOptionDescription>
            Individual components
        </RadioGroupOptionDescription>
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

<div x-show="package === 'components'">
## Components

Components are cool

```php
echo 'components';
```
</div>

</div>
