---
title: Users
---

By default, all `App\Models\User`s can access Filament locally. To allow them to access Filament in production, you must take a few extra steps to ensure that only the correct users have access to the app.

## Authorizing access to the app

To set up your `App\Models\User` to access Filament in non-local environments, you must implement the `FilamentUser` contract:

```php
<?php

namespace App\Models;

use Filament\Context;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    // ...

    public function canAccessFilament(Context $context): bool
    {
        return str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail();
    }
}
```

The `canAccessFilament()` method returns `true` or `false` depending on whether the user is allowed to access Filament. In this example, we check if the user's email ends with `@yourdomain.com` and if they have verified their email address.

## Setting up user avatars

Out of the box, Filament uses [ui-avatars.com](https://ui-avatars.com) to generate avatars based on a user's name. To provide your own avatar URLs, you can implement the `HasAvatar` contract:

```php
<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    // ...

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }
}
```

The `getFilamentAvatarUrl()` method is used to retrieve the avatar of the current user. If `null` is returned from this method, Filament will fall back to [ui-avatars.com](https://ui-avatars.com).

### Using a different avatar provider

You can easily swap out [ui-avatars.com](https://ui-avatars.com) for a different service, by creating a new avatar provider.

In this example, we create a new file at `app/Filament/AvatarProviders/BoringAvatarsProvider.php` for [boringavatars.com](https://boringavatars.com). The `get()` method accepts a user model instance and returns an avatar URL for that user:

```php
<?php

namespace App\Filament\AvatarProviders;

use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class BoringAvatarsProvider implements Contracts\AvatarProvider
{
    public function get(Model | Authenticatable $record): string
    {
        $name = str(Filament::getNameForDefaultAvatar($record))
            ->trim()
            ->explode(' ')
            ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
            ->join(' ');

        return 'https://source.boringavatars.com/beam/120/' . urlencode($name);
    }
}
```

Now, register this new avatar provider in the [configuration](configuration):

```php
use App\Filament\AvatarProviders\BoringAvatarsProvider;
use Filament\Context;

public function context(Context $context): Context
{
    return $context
        // ...
        ->defaultAvatarProvider(BoringAvatarsProvider::class);
}
```

## Configuring the user name attribute

By default, Filament will use the `name` attribute of the user to display their name in the app. To change this, you can implement the `HasName` contract:

```php
<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser, HasName
{
    // ...

    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
```

The `getFilamentName()` method is used to retrieve the name of the current user.

## Authentication features

You can easily enable authentication features for a context in the configuration file:

```php
use Filament\Context;

public function context(Context $context): Context
{
    return $context
        // ...
        ->login()
        ->registration()
        ->passwordReset()
        ->emailVerification();
}
```

### Customizing the authentication features

If you'd like to replace these pages with your own, you can pass in a "route action" to any of these methods.

A route action could be a callback function that gets executed when you visit the page, or the name of a controller, or a Livewire component - anything that works when using `Route::get()` in Laravel normally.

Most people will be able to make their desired customizations by extending the default Livewire class from the Filament codebase, overriding methods like `form()`, and then passing the new Livewire class in as the route action:

```php
use App\Http\Livewire\Auth\Login;
use Filament\Context;

public function context(Context $context): Context
{
    return $context
        // ...
        ->login(Login::class);
}
```
