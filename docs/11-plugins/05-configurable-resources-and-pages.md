---
title: Configurable resources and pages
---
import Aside from "@components/Aside.astro"

## Introduction

Sometimes you need to register the same resource or page multiple times with different configurations. For example, an "Orders" resource might appear as both "Active Orders" and "Archived Orders" in the sidebar, each with different query scopes, navigation labels, and URL slugs - but sharing the same underlying resource class.

Configurable resources and pages allow you to register a single class multiple times in a panel, each with a unique configuration key and its own set of options. Each configuration gets its own routes, navigation items, and URL slugs, while the resource or page class can use the active configuration to adjust its behavior at runtime.

<Aside variant="info">
    While this guide is in the plugins section, configurable resources and pages work in any `PanelProvider` - you don't need to be building a plugin to use them. They are especially useful for plugins because they let plugin authors expose flexible configuration to their users.
</Aside>

## Creating a resource configuration class

To make a resource configurable, you first need a configuration class. This class extends `ResourceConfiguration` and defines the options that can vary between registrations:

```php
use Filament\Resources\ResourceConfiguration;

class OrderResourceConfiguration extends ResourceConfiguration
{
    protected bool $isArchived = false;

    protected ?string $navigationLabel = null;

    protected ?string $navigationGroup = null;

    public function archived(bool $condition = true): static
    {
        $this->isArchived = $condition;

        return $this;
    }

    public function isArchived(): bool
    {
        return $this->isArchived;
    }

    public function navigationLabel(string $label): static
    {
        $this->navigationLabel = $label;

        return $this;
    }

    public function getNavigationLabel(): ?string
    {
        return $this->navigationLabel;
    }

    public function navigationGroup(string $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function getNavigationGroup(): ?string
    {
        return $this->navigationGroup;
    }
}
```

The configuration class follows the same [fluent API patterns](../plugins/panel-plugins#configuring-plugins-per-panel) as the rest of Filament - setter methods return `$this` for chaining, and getter methods retrieve stored values.

<Aside variant="tip">
    The `ResourceConfiguration` base class already includes a `slug()` method for overriding the URL slug. You only need to add properties specific to your plugin.
</Aside>

## Linking the configuration class to a resource

Set the `$configurationClass` property on your resource to link it with the configuration class:

```php
use Filament\Resources\Resource;

class OrderResource extends Resource
{
    protected static ?string $configurationClass = OrderResourceConfiguration::class;

    // ...
}
```

This enables the `make()` method on the resource, which creates a new configuration instance that can be registered on a panel.

## Registering configurations on a panel

You may register one or more configurations using the `make()` method. Each configuration needs a unique key:

```php
use App\Filament\Resources\OrderResource;

public function panel(Panel $panel): Panel
{
    return $panel
        ->resources([
            // "Active orders" configuration
            OrderResource::make('active')
                ->navigationLabel('Active Orders')
                ->navigationGroup('Orders'),
            // "Archived orders" configuration
            OrderResource::make('archived')
                ->navigationLabel('Archived Orders')
                ->navigationGroup('Orders')
                ->archived(),
        ]);
}
```

Each configured registration gets its own routes and navigation items. The configuration key (`'active'`, `'archived'`) is used internally to identify each registration.

You may also register the resource class on its own alongside configurations if you want a default (unconfigured) registration as well. This is optional - you can register only configurations if that's all you need:

```php
$panel->resources([
    OrderResource::class, // Optional default registration
    OrderResource::make('active'),
    OrderResource::make('archived')
        ->archived(),
]);
```

<Aside variant="tip">
    If you're building a [plugin class](panel-plugins#configuring-the-panel-with-a-plugin-class), you would register configurations inside the `register(Panel $panel)` method instead. See [using configurable resources in a plugin class](#using-configurable-resources-in-a-plugin-class) for a complete example.
</Aside>

### URL slugs

When you register the resource class on its own (without a configuration), it uses the resource's default URL slug - for example, `/orders`.

When you register a configuration with a key, the key is appended to the resource's base slug. For example, `OrderResource::make('active')` would be accessible at `/orders/active`, and `OrderResource::make('archived')` at `/orders/archived`.

You may use `slug()` to override the entire slug for a configuration instead of using the default `{base}/{key}` pattern:

```php
OrderResource::make('archived')
    ->slug('order-archive') // accessible at `/order-archive` instead of `/orders/archived`
    ->archived(),
```

## Using the configuration at runtime

Inside your resource class, call `static::getConfiguration()` to retrieve the active configuration for the current request. This returns `null` when the resource is accessed via its default (unconfigured) registration:

```php
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $configurationClass = OrderResourceConfiguration::class;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if ($configuration = static::getConfiguration()) {
            if ($configuration->isArchived()) {
                $query->where('archived_at', '!=', null);
            }
        }

        return $query;
    }

    public static function getNavigationLabel(): string
    {
        if ($configuration = static::getConfiguration()) {
            if ($label = $configuration->getNavigationLabel()) {
                return $label;
            }
        }

        return parent::getNavigationLabel();
    }

    public static function getNavigationGroup(): string | UnitEnum | null
    {
        if ($configuration = static::getConfiguration()) {
            if ($group = $configuration->getNavigationGroup()) {
                return $group;
            }
        }

        return parent::getNavigationGroup();
    }

    // ...
}
```

You can use `static::hasConfiguration()` as a shorthand to check if a configuration is currently active:

```php
if (static::hasConfiguration()) {
    // Running inside a configured registration
}
```

## Generating URLs for a specific configuration

When generating URLs for a configured resource, pass the `configuration` argument to `getUrl()`:

```php
// URL for the default (unconfigured) registration
OrderResource::getUrl();

// URL for the "active" configuration
OrderResource::getUrl(configuration: 'active');

// URL for a specific page within the "archived" configuration
OrderResource::getUrl('edit', ['record' => $order], configuration: 'archived');
```

When you're already inside a configured request (e.g., in a resource page), `getUrl()` automatically uses the current configuration context. You only need to pass the `configuration` argument when linking to a different configuration from the one you're currently in.

## Configurable pages

Pages follow the same pattern as resources. The key differences are:

- Your configuration class extends `PageConfiguration` instead of `ResourceConfiguration`
- You register configurations using `$panel->pages()` instead of `$panel->resources()`
- Since pages are Livewire components, you can read configuration values in `mount()`:

```php
use Filament\Pages\Page;

class SettingsPage extends Page
{
    protected static ?string $configurationClass = SettingsPageConfiguration::class;

    public function mount(): void
    {
        if ($configuration = static::getConfiguration()) {
            $this->settingsCategory = $configuration->getSettingsCategory();
        }
    }

    // ...
}
```

```php
$panel->pages([
    SettingsPage::make('general')
        ->slug('general-settings')
        ->settingsCategory('general'),
    SettingsPage::make('advanced')
        ->slug('advanced-settings')
        ->settingsCategory('advanced'),
]);
```

## Temporarily switching configuration context

You may use `withConfiguration()` to execute code in the context of a specific configuration. This is useful when you need to generate URLs or access configuration values for a registration other than the currently active one:

```php
$archivedUrl = OrderResource::withConfiguration('archived', function () {
    return OrderResource::getUrl('index');
});
```

## Using configurable resources in a plugin class

Here's a complete example of a plugin that exposes configurable resources to its users:

```php
use Filament\Contracts\Plugin;
use Filament\Panel;

class TasksPlugin implements Plugin
{
    /** @var array<TaskResourceConfiguration> */
    protected array $taskResourceConfigurations = [];

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'tasks';
    }

    /**
     * @param  array<TaskResourceConfiguration>  $configurations
     */
    public function taskResources(array $configurations): static
    {
        $this->taskResourceConfigurations = $configurations;

        return $this;
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            TaskResource::class,
            ...$this->taskResourceConfigurations,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
```

Users of the plugin can then register multiple task views:

```php
use Vendor\TasksPlugin\TasksPlugin;
use Vendor\TasksPlugin\TaskResource;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugin(
            TasksPlugin::make()
                ->taskResources([
                    TaskResource::make('my-tasks')
                        ->ownedByCurrentUser(),
                    TaskResource::make('team-tasks')
                        ->ownedByCurrentTeam(),
                ])
        );
}
```
