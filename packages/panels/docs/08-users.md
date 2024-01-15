---
title: Users
---

## Overview

By default, all `App\Models\User`s can access Filament locally. To allow them to access Filament in production, you must take a few extra steps to ensure that only the correct users have access to the app.

## Authorizing access to the panel

To set up your `App\Models\User` to access Filament in non-local environments, you must implement the `FilamentUser` contract:

```php
<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    // ...

    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail();
    }
}
```

The `canAccessPanel()` method returns `true` or `false` depending on whether the user is allowed to access the `$panel`. In this example, we check if the user's email ends with `@yourdomain.com` and if they have verified their email address.

Since you have access to the current `$panel`, you can write conditional checks for separate panels. For example, only restricting access to the admin panel while allowing all users to access the other panels of your app:

```php
<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    // ...

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail();
        }

        return true;
    }
}
```

## Authorizing access to Resources

See the [Authorization](resources/getting-started#authorization) section in the Resource documentation for controlling access to Resource pages and their data records.

## Setting up user avatars

Out of the box, Filament uses [ui-avatars.com](https://ui-avatars.com) to generate avatars based on a user's name. However, if your user model has an `avatar_url` attribute, that will be used instead. To customize how Filament gets a user's avatar URL, you can implement the `HasAvatar` contract:

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

use Filament\AvatarProviders\Contracts;
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
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->defaultAvatarProvider(BoringAvatarsProvider::class);
}
```

## Configuring the user's name attribute

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

You can easily enable authentication features for a panel in the configuration file:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->login()
        ->registration()
        ->passwordReset()
        ->emailVerification()
        ->profile();
}
```

### Customizing the authentication features

If you'd like to replace these pages with your own, you can pass in any Filament page class to these methods.

Most people will be able to make their desired customizations by extending the base page class from the Filament codebase, overriding methods like `form()`, and then passing the new page class in to the configuration:

```php
use App\Filament\Pages\Auth\EditProfile;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->profile(EditProfile::class);
}
```

In this example, we will customize the profile page. We need to create a new PHP class at `app/Filament/Pages/Auth/EditProfile.php`:

```php
<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('username')
                    ->required()
                    ->maxLength(255),
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }
}
```

This class extends the base profile page class from the Filament codebase. Other page classes you could extend include:

- `Filament\Pages\Auth\Login`
- `Filament\Pages\Auth\Register`
- `Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt`
- `Filament\Pages\Auth\PasswordReset\RequestPasswordReset`
- `Filament\Pages\Auth\PasswordReset\ResetPassword`

In the `form()` method of the example, we call methods like `getNameFormComponent()` to get the default form components for the page. You can customize these components as required. For all the available customization options, see the base `EditProfile` page class in the Filament codebase - it contains all the methods that you can override to make changes.

#### Customizing an authentication field without needing to re-define the form

If you'd like to customize a field in an authentication form without needing to define a new `form()` method, you could extend the specific field method and chain your customizations:

```php
use Filament\Forms\Components\Component;

protected function getPasswordFormComponent(): Component
{
    return parent::getPasswordFormComponent()
        ->revealable(false);
}
```

### Customizing the authentication route slugs

You can customize the URL slugs used for the authentication routes in the [configuration](configuration):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->loginRouteSlug('login')
        ->registrationRouteSlug('register')
        ->passwordResetRoutePrefix('password-reset')
        ->passwordResetRequestRouteSlug('request')
        ->passwordResetRouteSlug('reset')
        ->emailVerificationRoutePrefix('email-verification')
        ->emailVerificationPromptRouteSlug('prompt')
        ->emailVerificationRouteSlug('verify');
}
```

### Setting the authentication guard

To set the authentication guard that Filament uses, you can pass in the guard name to the `authGuard()` [configuration](configuration) method:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->authGuard('web');
}
```

### Setting the password broker

To set the password broker that Filament uses, you can pass in the broker name to the `authPasswordBroker()` [configuration](configuration) method:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->authPasswordBroker('users');
}
```

### Disabling revealable password inputs

By default, all password inputs in authentication forms are [`revealable()`](../forms/fields/text-input#revealable-password-inputs). This allows the user can see a plain text version of the password they're typing by clicking a button. To disable this feature, you can pass `false` to the `revealablePasswords()` [configuration](configuration) method:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->revealablePasswords(false);
}
```

You could also disable this feature on a per-field basis by calling `->revealable(false)` on the field object when [extending the base page class](#customizing-an-authentication-field-without-needing-to-re-define-the-form).

## Setting up guest access to a panel

By default, Filament expects to work with authenticated users only. To allow guests to access a panel, you need to avoid using components which expect a signed-in user (such as profiles, avatars), and remove the built-in Authentication middleware:

- Remove the default `Authenticate::class` from the `authMiddleware()` array in the panel configuration.
- Remove `->login()` and any other [authentication features](#authentication-features) from the panel.
- Remove the default `AccountWidget` from the `widgets()` array, because it reads the current user's data.

### Authorizing guests in policies

When present, Filament relies on [Laravel Model Policies](https://laravel.com/docs/authorization#generating-policies) for access control. To give read-access for [guest users in a model policy](https://laravel.com/docs/authorization#guest-users), create the Policy and update the `viewAny()` and `view()` methods, changing the `User $user` param to `?User $user` so that it's optional, and `return true;`. Alternatively, you can remove those methods from the policy entirely.
