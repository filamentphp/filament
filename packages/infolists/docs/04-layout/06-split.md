---
title: Split
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

The `Split` component allows you to define layouts with flexible widths, using flexbox.

```php
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;

Split::make([
    Section::make([
        TextEntry::make('title')
            ->weight(FontWeight::Bold),
        TextEntry::make('content')
            ->markdown()
            ->prose(),
    ]),
    Section::make([
        TextEntry::make('created_at')
            ->dateTime(),
        TextEntry::make('published_at')
            ->dateTime(),
    ])->grow(false),
])->from('md')
```

In this example, the first section will `grow()` to consume available horizontal space, without affecting the amount of space needed to render the second section. This creates a sidebar effect.

The `from()` method is used to control the [Tailwind breakpoint](https://tailwindcss.com/docs/responsive-design#overview) (`sm`, `md`, `lg`, `xl`, `2xl`) at which the split layout should be used. In this example, the split layout will be used on medium devices and larger. On smaller devices, the sections will stack on top of each other.

<AutoScreenshot name="infolists/layout/split/simple" alt="Split" version="3.x" />
