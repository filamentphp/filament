---
title: Select Blade component
---

## Overview

The select component is a wrapper around the native `<select>` element. It provides a simple interface for selecting a single value from a list of options:

```blade
<x-filament::input.affixes>
    <x-filament::input.select wire:model="status">
        <option value="draft">Draft</option>
        <option value="reviewing">Reviewing</option>
        <option value="published">Published</option>
    </x-filament::input.select>
</x-filament::input.affixes>
```

To use the select component, you must wrap it in an "input affixes" component, which provides a border and other elements such as a prefix or suffix. You can learn mode about customizing the input affixes component [here](input-affixes).
