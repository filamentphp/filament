---
title: Split
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

The `Split` component allows you to define layouts with flexible widths, using flexbox.

```php
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

Split::make([
    Section::make([
        TextInput::make('title'),
        Textarea::make('content'),
    ]),
    Section::make([
        Toggle::make('is_published'),
        Toggle::make('is_featured'),
    ])->grow(false),
])->from('md')
```

In this example, the first section will `grow()` to consume available horizontal space, without affecting the amount of space needed to render the second section. This creates a sidebar effect.

The `from()` method is used to control the [Tailwind breakpoint](https://tailwindcss.com/docs/responsive-design#overview) (`sm`, `md`, `lg`, `xl`, `2xl`) at which the split layout should be used. In this example, the split layout will be used on medium devices and larger. On smaller devices, the sections will stack on top of each other.

### Horizontal Placement

The `Split` component will act as a **flexbox** when all its children are non-growable (`->grow(false)`) and the Horizontal placement can be managed using `->horizontalAlignment( Filament\Support\Enums\HorizontalAlignment::Evenly )` method. 
- `HorizontalAlignment::Start` - Justify content to the start of the container.
- `HorizontalAlignment::Center` - Justify content to the center of the container.
- `HorizontalAlignment::End` - Justify content to the end of the container.
- `HorizontalAlignment::Evenly` - Justify content with even spacing between them.
- `HorizontalAlignment::Between` - Justify content with space between them.
- `HorizontalAlignment::Around` - Justify content with space around them.

### Gap

The **Gap** between the children of the `Split` component can be controlled using the `->gap()` method. The gap can be set to a specific value or a Tailwind CSS spacing utility class.

```php
use Filament\Forms\Components\Split;

Split::make([])
    ->from('md')
    ->gap(4) // will be translated to `gap-4` tailwind class
```

<AutoScreenshot name="forms/layout/split/simple" alt="Split" version="3.x" />
