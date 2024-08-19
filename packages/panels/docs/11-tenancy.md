---
title: Multi-tenancy
---

## Overview

Multi-tenancy is a concept where a single instance of an application serves multiple customers. Each customer has their own data and access rules that prevent them from viewing or modifying each other's data. This is a common pattern in SaaS applications. Users often belong to groups of users (often called teams or organizations). Records are owned by the group, and users can be members of multiple groups. This is suitable for applications where users need to collaborate on data.

Multi-tenancy is a very sensitive topic. It's important to understand the security implications of multi-tenancy and how to properly implement it. If implemented partially or incorrectly, data belonging to one tenant may be exposed to another tenant. Filament provides a set of tools to help you implement multi-tenancy in your application, but it is up to you to understand how to use them. Filament does not provide any guarantees about the security of your application. It is your responsibility to ensure that your application is secure. Please see the [security](#tenancy-security) section for more information.

## Simple one-to-many tenancy

The term "multi-tenancy" is broad and may mean different things in different contexts. Filament's tenancy system implies that the user belongs to **many** tenants (*organizations, teams, companies, etc.*) and may switch between them.

If your case is simpler and you don't need a many-to-many relationship, then you don't need to set up the tenancy in Filament. You could use [observers](https://laravel.com/docs/eloquent#observers) and [global scopes](https://laravel.com/docs/eloquent#global-scopes) instead.

Let's say you have a database column `users.team_id`, you can scope all records to have the same `team_id` as the user using a [global scope](https://laravel.com/docs/eloquent#global-scopes):

```php
use Illuminate\Database\Eloquent\Builder;

class Post extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope('team', function (Builder $query) {
            if (auth()->hasUser()) {
                $query->where('team_id', auth()->user()->team_id);
                // or with a `team` relationship defined:
                $query->whereBelongsTo(auth()->user()->team);
            }
        });
    }
}
```

To automatically set the `team_id` on the record when it's created, you can create an [observer](https://laravel.com/docs/eloquent#observers):

```php
class PostObserver
{
    public function creating(Post $post): void
    {
        if (auth()->hasUser()) {
            $post->team_id = auth()->user()->team_id;
            // or with a `team` relationship defined:
            $post->team()->associate(auth()->user()->team);
        }
    }
}
```

## Setting up tenancy

To set up tenancy, you'll need to specify the "tenant" (like team or organization) model in the [configuration](configuration):

```php
use App\Models\Team;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->tenant(Team::class);
}
```

You'll also need to tell Filament which tenants a user belongs to. You can do this by implementing the `HasTenants` interface on the `App\Models\User` model:

```php
<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    // ...

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->teams;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->teams()->whereKey($tenant)->exists();
    }
}
```

In this example, users belong to many teams, so there is a `teams()` relationship. The `getTenants()` method returns the teams that the user belongs to. Filament uses this to list the tenants that the user has access to.

For security, you also need to implement the `canAccessTenant()` method of the `HasTenants` interface to prevent users from accessing the data of other tenants by guessing their tenant ID and putting it into the URL.

You'll also want users to be able to [register new teams](#adding-a-tenant-registration-page).

## Adding a tenant registration page

A registration page will allow users to create a new tenant.

When visiting your app after logging in, users will be redirected to this page if they don't already have a tenant.

To set up a registration page, you'll need to create a new page class that extends `Filament\Pages\Tenancy\RegisterTenant`. This is a full-page Livewire component. You can put this anywhere you want, such as `app/Filament/Pages/Tenancy/RegisterTeam.php`:

```php
namespace App\Filament\Pages\Tenancy;

use App\Models\Team;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;

class RegisterTeam extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register team';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                // ...
            ]);
    }

    protected function handleRegistration(array $data): Team
    {
        $team = Team::create($data);

        $team->members()->attach(auth()->user());

        return $team;
    }
}
```

You may add any [form components](../forms/getting-started) to the `form()` method, and create the team inside the `handleRegistration()` method.

Now, we need to tell Filament to use this page. We can do this in the [configuration](configuration):

```php
use App\Filament\Pages\Tenancy\RegisterTeam;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->tenantRegistration(RegisterTeam::class);
}
```

### Customizing the tenant registration page

You can override any method you want on the base registration page class to make it act as you want. Even the `$view` property can be overridden to use a custom view of your choice.

## Adding a tenant profile page

A profile page will allow users to edit information about the tenant.

To set up a profile page, you'll need to create a new page class that extends `Filament\Pages\Tenancy\EditTenantProfile`. This is a full-page Livewire component. You can put this anywhere you want, such as `app/Filament/Pages/Tenancy/EditTeamProfile.php`:

```php
namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;

class EditTeamProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Team profile';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                // ...
            ]);
    }
}
```

You may add any [form components](../forms/getting-started) to the `form()` method. They will get saved directly to the tenant model.

Now, we need to tell Filament to use this page. We can do this in the [configuration](configuration):

```php
use App\Filament\Pages\Tenancy\EditTeamProfile;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->tenantProfile(EditTeamProfile::class);
}
```

### Customizing the tenant profile page

You can override any method you want on the base profile page class to make it act as you want. Even the `$view` property can be overridden to use a custom view of your choice.

## Accessing the current tenant

Anywhere in the app, you can access the tenant model for the current request using `Filament::getTenant()`:

```php
use Filament\Facades\Filament;

$tenant = Filament::getTenant();
```

## Billing

### Using Laravel Spark

Filament provides a billing integration with [Laravel Spark](https://spark.laravel.com). Your users can start subscriptions and manage their billing information.

To install the integration, first [install Spark](https://spark.laravel.com/docs/installation.html) and configure it for your tenant model.

Now, you can install the Filament billing provider for Spark using Composer:

```bash
composer require filament/spark-billing-provider
```

In the [configuration](configuration), set Spark as the `tenantBillingProvider()`:

```php
use Filament\Billing\Providers\SparkBillingProvider;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->tenantBillingProvider(new SparkBillingProvider());
}
```

Now, you're all good to go! Users can manage their billing by clicking a link in the tenant menu.

### Requiring a subscription

To require a subscription to use any part of the app, you can use the `requiresTenantSubscription()` configuration method:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->requiresTenantSubscription();
}
```

Now, users will be redirected to the billing page if they don't have an active subscription.

#### Requiring a subscription for specific resources and pages

Sometimes, you may wish to only require a subscription for certain [resources](resources/getting-started) and [custom pages](pages) in your app. You can do this by returning `true` from the `isTenantSubscriptionRequired()` method on the resource or page class:

```php
public static function isTenantSubscriptionRequired(Panel $panel): bool
{
    return true;
}
```

If you're using the `requiresTenantSubscription()` configuration method, then you can return `false` from this method to allow access to the resource or page as an exception.

### Writing a custom billing integration

Billing integrations are quite simple to write. You just need a class that implements the `Filament\Billing\Providers\Contracts\Provider` interface. This interface has two methods.

`getRouteAction()` is used to get the route action that should be run when the user visits the billing page. This could be a callback function, or the name of a controller, or a Livewire component - anything that works when using `Route::get()` in Laravel normally. For example, you could put in a simple redirect to your own billing page using a callback function.

`getSubscribedMiddleware()` returns the name of a middleware that should be used to check if the tenant has an active subscription. This middleware should redirect the user to the billing page if they don't have an active subscription.

Here's an example billing provider that uses a callback function for the route action and a middleware for the subscribed middleware:

```php
use App\Http\Middleware\RedirectIfUserNotSubscribed;
use Filament\Billing\Providers\Contracts\Provider;
use Illuminate\Http\RedirectResponse;

class ExampleBillingProvider implements Provider
{
    public function getRouteAction(): string
    {
        return function (): RedirectResponse {
            return redirect('https://billing.example.com');
        };
    }

    public function getSubscribedMiddleware(): string
    {
        return RedirectIfUserNotSubscribed::class;
    }
}
```

### Customizing the billing route slug

You can customize the URL slug used for the billing route using the `tenantBillingRouteSlug()` method in the [configuration](configuration):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->tenantBillingRouteSlug('billing');
}
```

## Customizing the tenant menu

The tenant-switching menu is featured in the admin layout. It's fully customizable.

To register new items to the tenant menu, you can use the [configuration](configuration):

```php
use App\Filament\Pages\Settings;
use Filament\Navigation\MenuItem;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->tenantMenuItems([
            MenuItem::make()
                ->label('Settings')
                ->url(fn (): string => Settings::getUrl())
                ->icon('heroicon-m-cog-8-tooth'),
            // ...
        ]);
}
```

### Customizing the registration link

To customize the registration link on the tenant menu, register a new item with the `register` array key:

```php
use Filament\Navigation\MenuItem;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->tenantMenuItems([
            'register' => MenuItem::make()->label('Register new team'),
            // ...
        ]);
}
```

### Customizing the profile link

To customize the profile link on the tenant menu, register a new item with the `profile` array key:

```php
use Filament\Navigation\MenuItem;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->tenantMenuItems([
            'profile' => MenuItem::make()->label('Edit team profile'),
            // ...
        ]);
}
```

### Customizing the billing link

To customize the billing link on the tenant menu, register a new item with the `billing` array key:

```php
use Filament\Navigation\MenuItem;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->tenantMenuItems([
            'billing' => MenuItem::make()->label('Manage subscription'),
            // ...
        ]);
}
```

### Conditionally hiding tenant menu items

You can also conditionally hide a tenant menu item by using the `visible()` or `hidden()` methods, passing in a condition to check. Passing a function will defer condition evaluation until the menu is actually being rendered:

```php
use Filament\Navigation\MenuItem;

MenuItem::make()
    ->label('Settings')
    ->visible(fn (): bool => auth()->user()->can('manage-team'))
    // or
    ->hidden(fn (): bool => ! auth()->user()->can('manage-team'))
```

### Sending a `POST` HTTP request from a tenant menu item

You can send a `POST` HTTP request from a tenant menu item by passing a URL to the `postAction()` method:

```php
use Filament\Navigation\MenuItem;

MenuItem::make()
    ->label('Lock session')
    ->postAction(fn (): string => route('lock-session'))
```

### Hiding the tenant menu

You can hide the tenant menu by using the `tenantMenu(false)`

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->tenantMenu(false);
}
```

However, this is a sign that Filament's tenancy feature is not suitable for your project. If each user only belongs to one tenant, you should stick to [simple one-to-many tenancy](#simple-one-to-many-tenancy).

## Setting up avatars

Out of the box, Filament uses [ui-avatars.com](https://ui-avatars.com) to generate avatars based on a user's name. However, if you user model has an `avatar_url` attribute, that will be used instead. To customize how Filament gets a user's avatar URL, you can implement the `HasAvatar` contract:

```php
<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Model;

class Team extends Model implements HasAvatar
{
    // ...

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }
}
```

The `getFilamentAvatarUrl()` method is used to retrieve the avatar of the current user. If `null` is returned from this method, Filament will fall back to [ui-avatars.com](https://ui-avatars.com).

You can easily swap out [ui-avatars.com](https://ui-avatars.com) for a different service, by creating a new avatar provider. [You can learn how to do this here.](users#using-a-different-avatar-provider)

## Configuring the tenant relationships

When creating and listing records associated with a Tenant, Filament needs access to two Eloquent relationships for each resource - an "ownership" relationship that is defined on the resource model class, and a relationship on the tenant model class. By default, Filament will attempt to guess the names of these relationships based on standard Laravel conventions. For example, if the tenant model is `App\Models\Team`, it will look for a `team()` relationship on the resource model class. And if the resource model class is `App\Models\Post`, it will look for a `posts()` relationship on the tenant model class.

### Customizing the ownership relationship name

You can customize the name of the ownership relationship used across all resources at once, using the `ownershipRelationship` argument on the `tenant()` configuration method. In this example, resource model classes have an `owner` relationship defined:

```php
use App\Models\Team;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->tenant(Team::class, ownershipRelationship: 'owner');
}
```

Alternatively, you can set the `$tenantOwnershipRelationshipName` static property on the resource class, which can then be used to customize the ownership relationship name that is just used for that resource. In this example, the `Post` model class has an `owner` relationship defined:

```php
use Filament\Resources\Resource;

class PostResource extends Resource
{
    protected static ?string $tenantOwnershipRelationshipName = 'owner';

    // ...
}
```

### Customizing the resource relationship name

You can set the `$tenantRelationshipName` static property on the resource class, which can then be used to customize the relationship name that is used to fetch that resource. In this example, the tenant model class has an `blogPosts` relationship defined:

```php
use Filament\Resources\Resource;

class PostResource extends Resource
{
    protected static ?string $tenantRelationshipName = 'blogPosts';

    // ...
}
```

## Configuring the slug attribute

When using a tenant like a team, you might want to add a slug field to the URL rather than the team's ID. You can do that with the `slugAttribute` argument on the `tenant()` configuration method:

```php
use App\Models\Team;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->tenant(Team::class, slugAttribute: 'slug');
}
```

## Configuring the name attribute

By default, Filament will use the `name` attribute of the tenant to display its name in the app. To change this, you can implement the `HasName` contract:

```php
<?php

namespace App\Models;

use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Model;

class Team extends Model implements HasName
{
    // ...

    public function getFilamentName(): string
    {
        return "{$this->name} {$this->subscription_plan}";
    }
}
```

The `getFilamentName()` method is used to retrieve the name of the current user.

## Setting the current tenant label

Inside the tenant switcher, you may wish to add a small label like "Active team" above the name of the current team. You can do this by implementing the `HasCurrentTenantLabel` method on the tenant model:

```php
<?php

namespace App\Models;

use Filament\Models\Contracts\HasCurrentTenantLabel;
use Illuminate\Database\Eloquent\Model;

class Team extends Model implements HasCurrentTenantLabel
{
    // ...

    public function getCurrentTenantLabel(): string
    {
        return 'Active team';
    }
}
```

## Setting the default tenant

When signing in, Filament will redirect the user to the first tenant returned from the `getTenants()` method.

Sometimes, you might wish to change this. For example, you might store which team was last active, and redirect the user to that team instead.

To customize this, you can implement the `HasDefaultTenant` contract on the user:

```php
<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Model implements FilamentUser, HasDefaultTenant, HasTenants
{
    // ...

    public function getDefaultTenant(Panel $panel): ?Model
    {
        return $this->latestTeam;
    }

    public function latestTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'latest_team_id');
    }
}
```

## Applying middleware to tenant-aware routes

You can apply extra middleware to all tenant-aware routes by passing an array of middleware classes to the `tenantMiddleware()` method in the [panel configuration file](configuration):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->tenantMiddleware([
            // ...
        ]);
}
```

By default, middleware will be run when the page is first loaded, but not on subsequent Livewire AJAX requests. If you want to run middleware on every request, you can make it persistent by passing `true` as the second argument to the `tenantMiddleware()` method:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->tenantMiddleware([
            // ...
        ], isPersistent: true);
}
```

## Adding a tenant route prefix

By default the URL structure will put the tenant ID or slug immediately after the panel path. If you wish to prefix it with another URL segment, use the `tenantRoutePrefix()` method:

```php
use App\Models\Team;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->path('admin')
        ->tenant(Team::class)
        ->tenantRoutePrefix('team');
}
```

Before, the URL structure was `/admin/1` for tenant 1. Now, it is `/admin/team/1`.

## Using a domain to identify the tenant

When using a tenant, you might want to use domain or subdomain routing like `team1.example.com/posts` instead of a route prefix like `/team1/posts` . You can do that with the `tenantDomain()` method, alongside the `tenant()` configuration method. The `tenant` argument corresponds to the slug attribute of the tenant model:

```php
use App\Models\Team;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->tenant(Team::class, slugAttribute: 'slug')
        ->tenantDomain('{tenant:slug}.example.com');
}
```

In the above examples, the tenants live on subdomains of the main app domain. You may also set the system up to resolve the entire domain from the tenant as well:

```php
use App\Models\Team;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->tenant(Team::class, slugAttribute: 'domain')
        ->tenantDomain('{tenant:domain}');
}
```

In this example, the `domain` attribute should contain a valid domain host, like `example.com` or `subdomain.example.com`.

> Note: When using a parameter for the entire domain (`tenantDomain('{tenant:domain}')`), Filament will register a [global route parameter pattern](https://laravel.com/docs/routing#parameters-global-constraints) for all `tenant` parameters in the application to be `[a-z0-9.\-]+`. This is because Laravel does not allow the `.` character in route parameters by default. This might conflict with other panels using tenancy, or other parts of your application that use a `tenant` route parameter.

## Disabling tenancy for a resource

By default, all resources within a panel with tenancy will be scoped to the current tenant. If you have resources that are shared between tenants, you can disable tenancy for them by setting the `$isScopedToTenant` static property to `false` on the resource class:

```php
protected static bool $isScopedToTenant = false;
```

### Disabling tenancy for all resources

If you wish to opt-in to tenancy for each resource instead of opting-out, you can call `Resource::scopeToTenant(false)` inside a service provider's `boot()` method or a middleware:

```php
use Filament\Resources\Resource;

Resource::scopeToTenant(false);
```

Now, you can opt-in to tenancy for each resource by setting the `$isScopedToTenant` static property to `true` on a resource class:

```php
protected static bool $isScopedToTenant = true;
```

## Tenancy security

It's important to understand the security implications of multi-tenancy and how to properly implement it. If implemented partially or incorrectly, data belonging to one tenant may be exposed to another tenant. Filament provides a set of tools to help you implement multi-tenancy in your application, but it is up to you to understand how to use them. Filament does not provide any guarantees about the security of your application. It is your responsibility to ensure that your application is secure.

Below is a list of features that Filament provides to help you implement multi-tenancy in your application:

- Automatic scoping of resources to the current tenant. The base Eloquent query that is used to fetch records for a resource is automatically scoped to the current tenant. This query is used to render the resource's list table, and is also used to resolve records from the current URL when editing or viewing a record. This means that if a user attempts to view a record that does not belong to the current tenant, they will receive a 404 error.

- Automatic association of new resource records to the current tenant.

And here are the things that Filament does not currently provide:

- Scoping of relation manager records to the current tenant. When using the relation manager, in the vast majority of cases, the query will not need to be scoped to the current tenant, since it is already scoped to the parent record, which is itself scoped to the current tenant. For example, if a `Team` tenant model had an `Author` resource, and that resource had a `posts` relationship and relation manager set up, and posts only belong to one author, there is no need to scope the query. This is because the user will only be able to see authors that belong to the current team anyway, and thus will only be able to see posts that belong to those authors. You can [scope the Eloquent query](resources/relation-managers#customizing-the-relation-manager-eloquent-query) if you wish.

- Form component and filter scoping. When using the `Select`, `CheckboxList` or `Repeater` form components, the `SelectFilter`, or any other similar Filament component which is able to automatically fetch "options" or other data from the database (usually using a `relationship()` method), this data is not scoped. The main reason for this is that these features often don't belong to the Filament Panel Builder package, and have no knowledge that they are being used within that context, and that a tenant even exists. And even if they did have access to the tenant, there is nowhere for the tenant relationship configuration to live. To scope these components, you need to pass in a query function that scopes the query to the current tenant. For example, if you were using the `Select` form component to select an `author` from a relationship, you could do this:

```php
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

Select::make('author_id')
    ->relationship(
        name: 'author',
        titleAttribute: 'name',
        modifyQueryUsing: fn (Builder $query) => $query->whereBelongsTo(Filament::getTenant()),
    );
```

### Using tenant-aware middleware to apply global scopes

It might be useful to apply global scopes to your Eloquent models while they are being used in your panel. This would allow you to forget about scoping your queries to the current tenant, and instead have the scoping applied automatically. To do this, you can create a new middleware class like `ApplyTenantScopes`:

```bash
php artisan make:middleware ApplyTenantScopes
```

Inside the `handle()` method, you can apply any global scopes that you wish:

```php
use App\Models\Author;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ApplyTenantScopes
{
    public function handle(Request $request, Closure $next)
    {
        Author::addGlobalScope(
            fn (Builder $query) => $query->whereBelongsTo(Filament::getTenant()),
        );

        return $next($request);
    }
}
```

You can now [register this middleware](#applying-middleware-to-tenant-aware-routes) for all tenant-aware routes, and ensure that it is used across all Livewire AJAX requests by making it persistent:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->tenantMiddleware([
            ApplyTenantScopes::class,
        ], isPersistent: true);
}
```
