---
title: Split
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

The `Split` component allows you to define layouts with flexible widths, using flexbox.

```php
use Filament\Infolists\Components\Card;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;

Split::make([
    Card::make([
        TextEntry::make('title')
            ->weight('bold'),
        TextEntry::make('content')
            ->markdown()
            ->prose(),
    ])->grow(),
    Card::make([
        TextEntry::make('created_at')
            ->dateTime(),
        TextEntry::make('published_at')
            ->dateTime(),
    ]),
])->from('md')
```

In this example, the first card will `grow()` to consume available vertical space, without affecting the amount of space needed to render the second card. This creates a sidebar effect.

The `from()` method is used to control the [Tailwind breakpoint](https://tailwindcss.com/docs/responsive-design#overview) (`sm`, `md`, `lg`, `xl`, `2xl`) at which the split layout should be used. In this example, the split layout will be used on medium devices and larger. On smaller devices, the cards will stack on top of each other.

<AutoScreenshot name="infolists/layout/split/simple" alt="Split" version="3.x" />
