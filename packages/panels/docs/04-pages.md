---
title: Pages
---

## Overview

Filament allows you to create completely custom pages for the app.

## Creating a page

To create a new page, you can use:

```bash
php artisan make:filament-page Settings
```

This command will create two files - a page class in the `/Pages` directory of the Filament directory, and a view in the `/pages` directory of the Filament views directory.

Page classes are all full-page [Livewire](https://livewire.laravel.com) components with a few extra utilities you can use with the panel.

## Authorization

You can prevent pages from appearing in the menu by overriding the `canAccess()` method in your Page class. This is useful if you want to control which users can see the page in the navigation, and also which users can visit the page directly:

```php
public static function canAccess(): bool
{
    return auth()->user()->canManageSettings();
}
```

## Adding actions to pages

Actions are buttons that can perform tasks on the page, or visit a URL. You can read more about their capabilities [here](../actions).

Since all pages are Livewire components, you can [add actions](../actions/adding-an-action-to-a-livewire-component#adding-the-action) anywhere. Pages already have the `InteractsWithActions` trait, `HasActions` interface, and `<x-filament-actions::modals />` Blade component all set up for you.

### Header actions

You can also easily add actions to the header of any page, including [resource pages](resources/getting-started). You don't need to worry about adding anything to the Blade template, we handle that for you. Just return your actions from the `getHeaderActions()` method of the page class:

```php
use Filament\Actions\Action;

protected function getHeaderActions(): array
{
    return [
        Action::make('edit')
            ->url(route('posts.edit', ['post' => $this->post])),
        Action::make('delete')
            ->requiresConfirmation()
            ->action(fn () => $this->post->delete()),
    ];
}
```

### Opening an action modal when a page loads

You can also open an action when a page loads by setting the `$defaultAction` property to the name of the action you want to open:

```php
use Filament\Actions\Action;

public $defaultAction = 'onboarding';

public function onboardingAction(): Action
{
    return Action::make('onboarding')
        ->modalHeading('Welcome')
        ->visible(fn (): bool => ! auth()->user()->isOnBoarded());
}
```

You can also pass an array of arguments to the default action using the `$defaultActionArguments` property:

```php
public $defaultActionArguments = ['step' => 2];
```

Alternatively, you can open an action modal when a page loads by specifying the `action` as a query string parameter to the page:

```
/admin/products/edit/932510?action=onboarding
```

### Refreshing form data

If you're using actions on an [Edit](resources/editing-records) or [View](resources/viewing-records) resource page, you can refresh data within the main form using the `refreshFormData()` method:

```php
use App\Models\Post;
use Filament\Actions\Action;

Action::make('approve')
    ->action(function (Post $record) {
        $record->approve();

        $this->refreshFormData([
            'status',
        ]);
    })
```

This method accepts an array of model attributes that you wish to refresh in the form.

## Adding widgets to pages

Filament allows you to display [widgets](dashboard) inside pages, below the header and above the footer.

To add a widget to a page, use the `getHeaderWidgets()` or `getFooterWidgets()` methods:

```php
use App\Filament\Widgets\StatsOverviewWidget;

protected function getHeaderWidgets(): array
{
    return [
        StatsOverviewWidget::class
    ];
}
```

`getHeaderWidgets()` returns an array of widgets to display above the page content, whereas `getFooterWidgets()` are displayed below.

If you'd like to learn how to build and customize widgets, check out the [Dashboard](dashboard) documentation section.

### Customizing the widgets' grid

You may change how many grid columns are used to display widgets.

You may override the `getHeaderWidgetsColumns()` or `getFooterWidgetsColumns()` methods to return a number of grid columns to use:

```php
public function getHeaderWidgetsColumns(): int | array
{
    return 3;
}
```

#### Responsive widgets grid

You may wish to change the number of widget grid columns based on the responsive [breakpoint](https://tailwindcss.com/docs/responsive-design#overview) of the browser. You can do this using an array that contains the number of columns that should be used at each breakpoint:

```php
public function getHeaderWidgetsColumns(): int | array
{
    return [
        'md' => 4,
        'xl' => 5,
    ];
}
```

This pairs well with [responsive widget widths](dashboard#responsive-widget-widths).

#### Passing data to widgets from the page

You may pass data to widgets from the page using the `getWidgetsData()` method:

```php
public function getWidgetData(): array
{
    return [
        'stats' => [
            'total' => 100,
        ],
    ];
}
```

Now, you can define a corresponding public `$stats` array property on the widget class, which will be automatically filled:

```php
public $stats = [];
```

### Passing properties to widgets on pages

When registering a widget on a page, you can use the `make()` method to pass an array of [Livewire properties](https://livewire.laravel.com/docs/properties) to it:

```php
use App\Filament\Widgets\StatsOverviewWidget;

protected function getHeaderWidgets(): array
{
    return [
        StatsOverviewWidget::make([
            'status' => 'active',
        ]),
    ];
}
```

This array of properties gets mapped to [public Livewire properties](https://livewire.laravel.com/docs/properties) on the widget class:

```php
use Filament\Widgets\Widget;

class StatsOverviewWidget extends Widget
{
    public string $status;

    // ...
}
```

Now, you can access the `status` in the widget class using `$this->status`.

## Customizing the page title

By default, Filament will automatically generate a title for your page based on its name. You may override this by defining a `$title` property on your page class:

```php
protected static ?string $title = 'Custom Page Title';
```

Alternatively, you may return a string from the `getTitle()` method:

```php
use Illuminate\Contracts\Support\Htmlable;

public function getTitle(): string | Htmlable
{
    return __('Custom Page Title');
}
```

## Customizing the page navigation label

By default, Filament will use the page's [title](#customizing-the-page-title) as its [navigation](navigation) item label. You may override this by defining a `$navigationLabel` property on your page class:

```php
protected static ?string $navigationLabel = 'Custom Navigation Label';
```

Alternatively, you may return a string from the `getNavigationLabel()` method:

```php
public static function getNavigationLabel(): string
{
    return __('Custom Navigation Label');
}
```

## Customizing the page URL

By default, Filament will automatically generate a URL (slug) for your page based on its name. You may override this by defining a `$slug` property on your page class:

```php
protected static ?string $slug = 'custom-url-slug';
```

## Customizing the page heading

By default, Filament will use the page's [title](#customizing-the-page-title) as its heading. You may override this by defining a `$heading` property on your page class:

```php
protected ?string $heading = 'Custom Page Heading';
```

Alternatively, you may return a string from the `getHeading()` method:

```php
public function getHeading(): string
{
    return __('Custom Page Heading');
}
```

### Adding a page subheading

You may also add a subheading to your page by defining a `$subheading` property on your page class:

```php
protected ?string $subheading = 'Custom Page Subheading';
```

Alternatively, you may return a string from the `getSubheading()` method:

```php
public function getSubheading(): ?string
{
    return __('Custom Page Subheading');
}
```

## Replacing the page header with a custom view

You may replace the default [heading](#customizing-the-page-heading), [subheading](#adding-a-page-subheading) and [actions](#header-actions) with a custom header view for any page. You may return it from the `getHeader()` method:

```php
use Illuminate\Contracts\View\View;

public function getHeader(): ?View
{
    return view('filament.settings.custom-header');
}
```

This example assumes you have a Blade view at `resources/views/filament/settings/custom-header.blade.php`.

## Rendering a custom view in the footer of the page

You may also add a footer to any page, below its content. You may return it from the `getFooter()` method:

```php
use Illuminate\Contracts\View\View;

public function getFooter(): ?View
{
    return view('filament.settings.custom-footer');
}
```

This example assumes you have a Blade view at `resources/views/filament/settings/custom-footer.blade.php`.

## Customizing the maximum content width

By default, Filament will restrict the width of the content on the page, so it doesn't become too wide on large screens. To change this, you may override the `getMaxContentWidth()` method. Options correspond to [Tailwind's max-width scale](https://tailwindcss.com/docs/max-width). The options are `ExtraSmall`, `Small`, `Medium`, `Large`, `ExtraLarge`, `TwoExtraLarge`, `ThreeExtraLarge`, `FourExtraLarge`, `FiveExtraLarge`, `SixExtraLarge`, `SevenExtraLarge`, `Full`, `MinContent`, `MaxContent`, `FitContent`,  `Prose`, `ScreenSmall`, `ScreenMedium`, `ScreenLarge`, `ScreenExtraLarge` and `ScreenTwoExtraLarge`. The default is `SevenExtraLarge`:

```php
use Filament\Support\Enums\MaxWidth;

public function getMaxContentWidth(): MaxWidth
{
    return MaxWidth::Full;
}
```

## Generating URLs to pages

Filament provides `getUrl()` static method on page classes to generate URLs to them. Traditionally, you would need to construct the URL by hand or by using Laravel's `route()` helper, but these methods depend on knowledge of the page's slug or route naming conventions.

The `getUrl()` method, without any arguments, will generate a URL:

```php
use App\Filament\Pages\Settings;

Settings::getUrl(); // /admin/settings
```

If your page uses URL / query parameters, you should use the argument:

```php
use App\Filament\Pages\Settings;

Settings::getUrl(['section' => 'notifications']); // /admin/settings?section=notifications
```

### Generating URLs to pages in other panels

If you have multiple panels in your app, `getUrl()` will generate a URL within the current panel. You can also indicate which panel the page is associated with, by passing the panel ID to the `panel` argument:

```php
use App\Filament\Pages\Settings;

Settings::getUrl(panel: 'marketing');
```

## Adding sub-navigation between pages

You may want to add a common sub-navigation to multiple pages, to allow users to quickly navigate between them. You can do this by defining a [cluster](clusters). Clusters can also contain [resources](resources), and you can switch between multiple pages or resources within a cluster.

## Adding extra attributes to the body tag of a page

You may wish to add extra attributes to the `<body>` tag of a page. To do this, you can set an array of attributes in `$extraBodyAttributes`:

```php
protected array $extraBodyAttributes = [];
```

Or, you can return an array of attributes and their values from the `getExtraBodyAttributes()` method:

```php
public function getExtraBodyAttributes(): array
{
    return [
        'class' => 'settings-page',
    ];
}
```
