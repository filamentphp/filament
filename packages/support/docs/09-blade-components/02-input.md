---
title: Input Blade component
---

## Overview

The input component is a wrapper around the native `<input>` element. It provides a simple interface for entering a single line of text.

```blade
<x-filament::input.wrapper>
    <x-filament::input
        type="text"
        wire:model="name"
    />
</x-filament::input.wrapper>
```

To use the input component, you must wrap it in an "input wrapper" component, which provides a border and other elements such as a prefix or suffix. You can learn more about customizing the input wrapper component [here](input-wrapper).
