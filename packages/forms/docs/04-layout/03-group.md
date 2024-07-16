---
title: Group
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

`Group` is not actually a layout component, as it has no styling associated with it.

```php
use Filament\Forms\Components\Group;

Group::make()
    ->schema([
        // ...
    ])
```

<!-- Screenshot? -->

## Relationships

You may associate fields inside `Group` schema to a related model:

```php
use Filament\Forms\Components\Group;

Group::make()
    ->relationship('model')
    ->schema([
        // ...
    ])
```
