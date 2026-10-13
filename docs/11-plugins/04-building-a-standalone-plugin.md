---
title: Build a standalone plugin
---

## Introduction

In this walkthrough, we'll build a simple plugin that adds a new form component that can be used in forms. This also means it will be available to users in their panels.

You can find the original plugin at [awcodes/headings](https://github.com/awcodes/headings). The examples below use Filament 4 APIs.

## Preface

Please read the [getting started guide](getting-started) and the documentation on [custom form fields](../forms/custom-fields) before continuing.

## Step 1: Create the plugin

First, we'll create the plugin using the steps outlined in the [getting started guide](getting-started#creating-a-plugin).

## Step 2: Clean up

Next, we'll clean up the plugin to remove the boilerplate code we don't need. This will seem like a lot, but since this is a simple plugin, we can remove a lot of the boilerplate code.

Remove the following directories and files:
1. `bin`
2. `config`
3. `database`
4. `src/Commands`
5. `src/Facades`
6. `stubs`

Remove the database factory autoload mapping and facade alias from `composer.json`. Keep the `Awcodes\Headings\` mapping for `src/` and the service provider registration under `extra.laravel.providers`.

For this example, you'll provide a stylesheet that loads only where the component is used, using `x-load-css`. Update `package.json` to include `cssnano`, `postcss`, `postcss-cli` and `postcss-nesting` to build it.

```json
{
    "private": true,
    "scripts": {
        "build": "postcss resources/css/index.css -o resources/dist/headings.css"
    },
    "devDependencies": {
        "cssnano": "^6.0.1",
        "postcss": "^8.4.27",
        "postcss-cli": "^10.1.0",
        "postcss-nesting": "^13.0.0"
    }
}
```

Then we need to install our dependencies.

```bash
npm install
```

We will also need to update our `postcss.config.js` file to configure postcss.

```js
module.exports = {
    plugins: [
        require('postcss-nesting')(),
        require('cssnano')({
            preset: 'default',
        }),
    ],
};
```

You may also remove the testing directories and files, but we'll leave them in for now, although we won't be using them for this example, and we highly recommend that you write tests for your plugins.

## Step 3: Setting up the provider

Now that we have our plugin cleaned up, we can start adding our code. The boilerplate in the `src/HeadingsServiceProvider.php` file has a lot going on so, let's delete everything and start from scratch.

We need to be able to register our stylesheet with the Filament Asset Manager so that we can load it on demand in our Blade view. To do this, we'll need to add the following to the `packageBooted` method in our service provider.

***Note the `loadedOnRequest()` method. This is important, because it tells Filament to only load the stylesheet when it's needed.***

```php
namespace Awcodes\Headings;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class HeadingsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'headings';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews();
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Css::make('headings', __DIR__ . '/../resources/dist/headings.css')->loadedOnRequest(),
        ], 'awcodes/headings');
    }
}
```

## Step 4: Creating our component

Next, we'll need to create our component. Create a new file at `src/Heading.php` and add the following code.

```php
namespace Awcodes\Headings;

use Closure;
use Filament\Schemas\Components\Component;
use Filament\Support\Colors\Color;
use Filament\Support\Concerns\HasColor;

class Heading extends Component
{
    use HasColor;

    protected string | int $level = 2;

    protected string | Closure $content = '';

    protected string $view = 'headings::heading';

    final public function __construct(string | int $level)
    {
        $this->level($level);
    }

    public static function make(string | int $level): static
    {
        $component = app(static::class, ['level' => $level]);
        $component->configure();

        return $component;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultColor(Color::Amber);
    }

    public function content(string | Closure $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function level(string | int $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getContent(): string
    {
        return $this->evaluate($this->content);
    }

    public function getLevel(): string
    {
        return is_int($this->level) ? 'h' . $this->level : $this->level;
    }
}
```

## Step 5: Rendering our component

Next, we'll need to create the view for our component. Create a new file at `resources/views/heading.blade.php` and add the following code.

Use `x-load-css` to load the stylesheet only where the component is rendered. The attribute bag's `color()` method supplies Filament's text color variables for both light and dark mode. See [lazy loading CSS](../advanced/assets#lazy-loading-css) and [colors](../styling/colors) for more information.

```blade
@php
    $level = $getLevel();
    $color = $getColor();
@endphp

<{{ $level }}
    x-data
    x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('headings', package: 'awcodes/headings'))]"
    {{
        $attributes
            ->class(['headings-component'])
            ->color(\Filament\Schemas\View\Components\TextComponent::class, $color)
    }}
>
    {{ $getContent() }}
</{{ $level }}>
```

## Step 6: Adding some styles

Next, let's provide some custom styling for our field. We'll add the following to `resources/css/index.css`. And run `npm run build` to compile our CSS.

```css
.headings-component {
    color: var(--gray-600);

    .dark & {
        color: var(--gray-400);
    }

    &.fi-color {
        color: var(--text);

        .dark & {
            color: var(--dark-text);
        }
    }

    &:is(h1, h2, h3, h4, h5, h6) {
         font-weight: 700;
         letter-spacing: -.025em;
         line-height: 1.1;
     }

    &:is(h1) {
         font-size: 2rem;
     }

    &:is(h2) {
         font-size: 1.75rem;
     }

    &:is(h3) {
         font-size: 1.5rem;
     }

    &:is(h4) {
         font-size: 1.25rem;
     }

    &:is(h5, h6) {
         font-size: 1rem;
     }
}
```

Then we need to build our stylesheet.

```bash
npm run build
```

## Step 7: Update your README

You'll want to update your `README.md` file to include instructions on how to install your plugin and any other information you want to share with users, Like how to use it in their projects. For example:

```php
use Awcodes\Headings\Heading;
use Filament\Support\Colors\Color;

Heading::make(2)
    ->content('Product Information')
    ->color(Color::Lime),
```

And, that's it, our users can now install our plugin and use it in their projects.
