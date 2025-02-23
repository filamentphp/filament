---
title: Decorations
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

Decorations are a way to add extra content to components. For example, you might want to add some text underneath a form field to provide additional context:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('url')
    ->belowContent('Your full name here, including any middle names.')
```

You can also render more complex decorations, like icons, by using a decoration component to configure it:

```php
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Icon;

TextInput::make('url')
    ->afterLabel(
        Icon::make('heroicon-m-information-circle')
            ->tooltip('Example: John Doe'),
    )
```

You can render multiple decorations in one slot, using an array of decoration components:

```php
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Text;

TextInput::make('name')
    ->belowContent([
        Text::make('Your full name here, including any middle names.'),
        Icon::make('heroicon-m-information-circle')
            ->tooltip('Example: John Doe'),
    ])
```

You can also align decorations using an `AlignDecorations` component, for example, to make the text occupy the left and the icon the far right:

```php
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Decorations\Layouts\AlignDecorations;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Text;

TextInput::make('name')
    ->belowContent(AlignDecorations::between([
        Text::make('Your full name here, including any middle names.'),
        Icon::make('heroicon-m-information-circle')
            ->tooltip('Example: John Doe'),
    ]))
```

## Available decoration slots

Different schema components have different slots available for decorations:

All [form fields](../forms/fields#available-fields) and [infolist entries](../infolists/entries#available-entries) can use:

- `aboveContent()` - above the field content, start (left) aligned by default.
- `aboveLabel()` - above the field label, start (left) aligned by default.
- `afterContent()` - horizontally after the field content, end (right) aligned by default.
- `afterLabel()` - horizontally after the field label, end (right) aligned by default.
- `beforeContent()` - horizontally before the field content, start (left) aligned by default.
- `beforeLabel()` - horizontally before the field label, start (left) aligned by default.
- `belowContent()` - below the field content, start (left) aligned by default.
- `belowLabel()` - below the field label, start (left) aligned by default.

All [form fields](../forms/fields#available-fields) can also use:

- `aboveErrorMessage()` - above the error message, start (left) aligned by default. These are rendered regardless of whether the field has an error.
- `belowErrorMessage()` - below the error message, start (left) aligned by default. These are rendered regardless of whether the field has an error.

The [Form layout component](layout/form) can use:

- `footer()` - below the `Form` component content, start (left) aligned by default.
- `header()` - above the `Form` component content, start (left) aligned by default.

The [Section layout component](layout/section) can use:

- `afterHeader()` - horizontally after the `Section` component heading and description, end (right) aligned by default. These are rendered regardless of whether the `Section` has a heading or description.
- `footer()` - below the `Section` component content, start (left) aligned by default.

## Text decorations

Text decorations can be defined by passing a string as the decoration:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->belowContent('Your full name here, including any middle names.')
```

Alternatively, for more complex text decorations, and when using multiple decorations, you should use the `TextDecoration` component:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->belowContent(TextDecoration::make('Your full name here, including any middle names.'))
```

### Customizing the color of text decorations

You may set a [color](../styling/colors) for a text decoration using the `color()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->belowContent(
        TextDecoration::make('Your full name here, including any middle names.')
            ->color('info'),
    )
```

### Displaying a text decoration as a "badge"

By default, text is quite plain and has no background color. You can make it appear as a "badge" instead using the `badge()` method. A great use case for this is with statuses, where may want to display a badge with a [color](#customizing-the-color-of-text-decorations) that matches the status:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('url')
    ->belowContent(
        TextDecoration::make('Public')
            ->badge(),
    )
```

#### Adding an icon to a text decoration badge

Text decorations displayed as a badge may also have an [icon](../styling/icons):

```php
use Filament\Forms\Components\TextInput;use Filament\Schemas\Components\Text;

TextInput::make('url')
    ->belowContent(
        Text::make('Public')
            ->badge()
            ->icon('heroicon-m-globe-alt'),
    )
```

You may set the position of an icon using `iconPosition()`:

```php
use Filament\Forms\Components\TextInput;use Filament\Schemas\Components\Text;

TextInput::make('url')
    ->belowContent(
        Text::make('Public')
            ->badge()
            ->icon('heroicon-m-globe-alt')
            ->iconPosition(IconPosition::After) // `IconPosition::Before` or `IconPosition::After`,
    )
```

### Customizing the font family of a text decoration

You can change the text font family to any of the following options: `FontFamily::Sans`, `FontFamily::Serif` or `FontFamily::Mono`.

For instance, you may make the font monospaced using `fontFamily(FontFamily::Mono)`:

```php
use Filament\Forms\Components\TextInput;use Filament\Schemas\Components\Text;use Filament\Support\Enums\FontFamily;

TextInput::make('apiKey')
    ->belowContent([
        Text::make('Example'),
        Text::make('v1Fm7gyWGh5n9GPE')
            ->fontFamily(FontFamily::Mono),
    ])
```

### Adding a tooltip to a text decoration

You may specify a tooltip to display when you hover over a text decoration:

```php
use Filament\Forms\Components\TextInput;use Filament\Schemas\Components\Text;

TextInput::make('url')
    ->belowContent(
        Text::make('Public')
            ->badge()
            ->tooltip('Users will be able to see this on your profile.'),
    )
```

### Customizing the font weight of a text decoration

Text decoration have regular font weight by default, but you may change this to any of the following options: `FontWeight::Thin`, `FontWeight::ExtraLight`, `FontWeight::Light`, `FontWeight::Medium`, `FontWeight::SemiBold`, `FontWeight::Bold`, `FontWeight::ExtraBold` or `FontWeight::Black`.

For instance, you may make the font bold using `weight(FontWeight::Bold)`:

```php
use Filament\Forms\Components\TextInput;use Filament\Schemas\Components\Text;use Filament\Support\Enums\FontWeight;

TextInput::make('url')
    ->belowContent(
        Text::make('Public')
            ->weight(FontWeight::Bold),
    )
```

## Icon decorations

Icon decorations can be defined by using an `IconDecoration` component:

```php
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Icon;

TextInput::make('url')
    ->afterLabel(
        Icon::make('heroicon-m-information-circle'),
    )
```

### Customizing the color of an icon decoration

You may set a [color](../styling/colors) for an icon decoration using the `color()` method:

```php
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Icon;

TextInput::make('url')
    ->afterLabel(
        Icon::make('heroicon-m-information-circle')
            ->color('info'),
    )
```

### Adding a tooltip to an icon decoration

You may specify a tooltip to display when you hover over an icon decoration:

```php
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Icon;

TextInput::make('url')
    ->afterLabel(
        Icon::make('heroicon-m-information-circle')
            ->tooltip('Example: John Doe'),
    )
```

## Action decorations

[Actions](actions) can be used as decorations, to add interactive buttons to a component. For example, you might want to add a button to generate a random password:

```php
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;

TextInput::make('password')
    ->afterLabel(
        Action::make('generate')
            ->form([
                TextInput::make('length')
                    ->default(20)
                    ->integer()
                    ->minValue(10)
                    ->required(),
            ])
            ->action(function (TextInput $component, array $data) {
                $component->state(Str::password($data['length']));
            }),
    )
```

## Aligning decorations

You may align decorations within their slot. Each slot has a default alignment depending on where it is, but you may override this by wrapping your decorations in an `AlignDecorations` component. The available alignment options are `AlignDecorations::start()`, `AlignDecorations::end()`, and `AlignDecorations::between()`:

```php
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Decorations\Layouts\AlignDecorations;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Text;

TextInput::make('name')
    ->afterLabel(AlignDecorations::start([
        Icon::make('heroicon-m-information-circle')
            ->tooltip('Example: John Doe'),
    ]))

TextInput::make('name')
    ->belowContent(AlignDecorations::between([
        Text::make('Your full name here, including any middle names.'),
        Icon::make('heroicon-m-information-circle')
            ->tooltip('Example: John Doe'),
    ]))

TextInput::make('name')
    ->belowContent(AlignDecorations::end([
        Icon::make('heroicon-m-information-circle')
            ->tooltip('Example: John Doe'),
    ]))
```

When using `AlignDecorations::between()`, you may group decorations together at the start or end of the slot using a nested array:

```php
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Decorations\Layouts\AlignDecorations;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Text;

TextInput::make('name')
    ->belowContent(AlignDecorations::between([
        [
            Text::make('Your full name here, including any middle names.'),
            Icon::make('heroicon-m-information-circle')
                ->tooltip('Example: John Doe'),
        ],
        Action::make('switchToCompanyAccount')
            ->action(function () {
                // ...
            }),
    ]))
```

## Adding decorations to a custom component

If you are building a custom component, you may wish to allow the consumer of the component to inject decorations into its view.

If your custom component is a form field or an infolist entry, it will already benefit from many of the [available decoration slots](#available-decoration-slots) if it uses the built-in field wrapper or entry wrapper component from Filament.

Alternatively, you can create a custom method to accept decorations in your own slot. You should create a constant to store the name of the slot, avoiding the need to repeat the same string in multiple places when referencing these decorations. For example, you may wish to implement `footer()` decorations similar to the `Form` and `Section` layout components. The constant should be added to the component's PHP class:

```php
const FOOTER_SCHEMA_KEY = 'footer';
```

Now, you need a method that consumers of the component can use to add decorations to this slot. The method should call the `$this->decorations()` method, passing the value of the slot constant as an identifier alongside the decorations that the user passes in:

```php
/**
 * @param  array<Component | Action | ActionGroup> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
 */
public function footer(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
{
    $this->decorations(static::FOOTER_SCHEMA_KEY, $decorations);

    return $this;
}
```

By default, these decorations will be aligned to the start (left) of the slot if an alignment layout decoration is not provided. If you want to provide a different default alignment layout for these decorations, such as the end (right) of the container, you can pass a third argument: `makeDefaultLayoutUsing`. This argument should contain a function that gets run if the user does not specify their own layout component, which wraps the array of decorations that the consumer provides in the layout of your choice:

```php
$this->decorations(
    static::AFTER_HEADER_SCHEMA_KEY,
    $decorations,
    makeDefaultLayoutUsing: fn (array $decorations): AlignDecorations => AlignDecorations::end($decorations),
);
```

Finally, you can render the decorations from this slot in the view by calling the `$getDecorations()` function, passing in the identifier constant of the slot:

```blade
{{ $getDecorations($schemaComponent::FOOTER_SCHEMA_KEY) }}
```
