---
title: Upgrading from v1.x
---

> If you see anything missing from this guide, please do not hesitate to [make a pull request](https://github.com/laravel-filament/filament/edit/2.x/packages/admin/docs/09-upgrade-guide.md) to our repository! Any help is appreciated!

## High impact changes

### Property and method changes to resource and page classes

<details>
<summary>
Changes to Resource classes
</summary>

- The `Filament\Resources\Forms\Form` class has been renamed to `Filament\Resources\Form`.
- The `Filament\Resources\Tables\Table` class has been renamed to `Filament\Resources\Table`.

The following properties and method signatures been updated:

```php
protected static ?string $label; // Protected the property. Added the `?string` type.

protected static ?string $model; // Protected the property. Added the `?string` type.

protected static ?string $navigationIcon; // Renamed from `$icon`. Protected the property. Added the `?string` type.

protected static ?string $navigationLabel; // Protected the property. Added the `?string` type.

protected static ?int $navigationSort; // Protected the property. Added the `?int` type.

protected static ?string $recordTitleAttribute; // Renamed from `$primaryColumn`. Protected the property. Added the `?string` type.

protected static ?string $slug; // Protected the property. Added the `?string` type.

public static function form(Form $form): Form; // Added the `Form` return type.

public static function table(Table $table): Table; // Added the `Table` return type.

public static function getRelations(): array; // Renamed from `relations()`. Added the `array` return type.

public static function getPages(): array; // Renamed from `routes()`. Added the `array` return type.
```

The syntax for registering pages in `getPages()` (formerly `routes()`) has been updated:

```php
public static function getPages(): array
{
    return [
        'index' => Pages\ListUsers::route('/'),
        'create' => Pages\CreateUser::route('/create'),
        'edit' => Pages\EditUser::route('/{record}/edit'),
    ];
}
```
</details>

<details>
<summary>
Changes to List page classes
</summary>

The following properties and method signatures been updated:

```php
protected static string $resource; // Protected the property. Added the `string` type.
```
</details>

<details>
<summary>
Changes to Create page classes
</summary>

The following properties and method signatures been updated:

```php
protected static string $resource; // Protected the property. Added the `string` type.
```
</details>

<details>
<summary>
Changes to Edit page classes
</summary>

The following properties and method signatures been updated:

```php
protected static string $resource; // Protected the property. Added the `string` type.
```
</details>

### Forms

The entire `Filament\Resources\Forms` namespace has been moved to `Filament\Forms`.

Layout components, such as Grid and Tabs, now have their own separate `schema()` for form components, instead of using a parameter of the `make()` method. For more information, check out the [form builder layout documentation](/docs/forms/layout).

The `Filament\Resources\Forms\Tab` component has been moved to `Filament\Forms\Tabs\Tab`.

### Tables

The entire `Filament\Resources\Tables` namespace has been moved to `Filament\Tables`.

Column class names now have `Column` at the end, for example `TextColumn` not `Text`.

Filters now have a dedicated `query()` method for applying the query, instead of using the second parameter of the `make()` method. For more information, check out the [table builder filters documentation](/docs/tables/filters).

Method changes:

- The `primary()` method has been removed from columns. All columns link to the record page by default unless another URL or action is specified for that column.
- The `getValueUsing()` method has been renamed to `getStateUsing()`.
- The `currency()` method has been renamed to `money()`.

### Published configuration updates

If you've published the v1.x `filament.php` configuration file, you should republish it:

```bash
php artisan vendor:publish --tag=filament-config --force
```

If you had customized the `path`, `domain` or `default_filesystem_disk`, you should update the new file with these changes. If you're using `.env` variables for these settings, you won't need to make any changes when upgrading, and you may even choose to delete `filament.php`.

### Users

Filament v2.x does not include a dedicated `filament_users` table as it did in v1.x. By default, all `App\Models\User`s can access the admin panel locally, and in production you must apply the `FilamentUser` interface to the model to control admin access. You can read more about this [here](users).

<details>
<summary>
Are you using the <code>filament_users</code> table, and would like to continue using it?
</summary>

To keep the `filament_users` and `filament_password_resets` tables in your app, you'll need to copy the old migrations and model into your app.

<details>
<summary>
<code>database/migrations/0000_00_00_000000_create_filament_users_table.php</code>
</summary>

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilamentUsersTable extends Migration
{
    public function up(): void
    {
        Schema::create('filament_users', function (Blueprint $table): void {
            $table->id();
            $table->string('avatar')->nullable();
            $table->string('email')->unique();
            $table->string('name');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filament_users');
    }
}
```
</details>

<details>
<summary>
<code>database/migrations/0000_00_00_000001_create_filament_password_resets_table.php</code>
</summary>

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilamentPasswordResetsTable extends Migration
{
    public function up(): void
    {
        Schema::create('filament_password_resets', function (Blueprint $table): void {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filament_password_resets');
    }
}
```
</details>

<details>
<summary>
<code>app/Models/FilamentUser.php</code>
</summary>

```php
<?php

namespace App\Models;

use Filament\Models\Contracts;
use Illuminate\Foundation\Auth\User as Authenticatable;

class FilamentUser extends Authenticatable implements Contracts\FilamentUser, Contracts\HasAvatar
{
    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    public function canAccessFilament(): bool
    {
        return true;
    }
    
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar;
    }
}
```
</details>
</details>

<details>
<summary>
Are you using the <code>filament_users</code> table, but would like to switch to <code>App\Models\User</code>?
</summary>

Create a migration to drop the `filament_users` and `filament_password_resets` tables:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class DropFilamentUsersAndFilamentPasswordResetsTables extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('filament_users');
        Schema::dropIfExists('filament_password_resets');
    }
}
```
</details>

<details>
<summary>
Are you already using <code>App\Models\User</code>?
</summary>

1) Remove the `IsFilamentUser` trait from the model.
2) Remove the `$filamentUserColumn` property if you use them. Instead, control admin access with `canAccessFilament()`.
3) If you have a `canAccessFilament()` method, add a `bool` return type to it.
4) Remove the `$filamentAdminColumn` and `$filamentRolesColumn` properties, and `isFilamentAdmin()` method, if you use them. Filament now only uses policies for authorization, so you may implement whichever roles system you wish there. We recommend [`spatie/laravel-permission`](https://github.com/spatie/laravel-permission).
</details>

### `Filament\Filament` facade renamed to `Filament\Facades\Filament`

You should be able to safely rename all instances of this class to the new one.

## Medium impact changes

### Relation managers

- `HasMany` and `MorphMany` relation manager classes should now extend `Filament\Resources\RelationManagers\HasManyRelationManager`.
- `BelongsToMany` relation manager classes should now extend `Filament\Resources\RelationManagers\BelongsToManyRelationManager`.
- The `Filament\Resources\Forms\Form` class has been renamed to `Filament\Resources\Form`.
- The `Filament\Resources\Tables\Table` class has been renamed to `Filament\Resources\Table`.

The following properties and method signatures been updated:

```php
protected static ?string $inverseRelationship; // Protected the property. Added the `?string` type.

protected static ?string $recordTitleAttribute; // Renamed from `$primaryColumn`. Protected the property. Added the `?string` type.

protected static string $relationship; // Protected the property. Added the `string` type.
```

### Roles

Filament now only uses policies for authorization, so you may implement whichever roles system you wish there. We recommend [`spatie/laravel-permission`](https://github.com/spatie/laravel-permission).

You may remove any roles from the `App\Filament\Roles` directory, and delete any `authorization()` methods on your resources.

## Low impact changes

### `Filament::ignoreMigrations()` method removed

Since Filament doesn't have any migrations anymore, you don't need to ignore them.

### Property changes to custom page classes

The following properties and method signatures been updated:

```php
protected static ?string $title; // Protected the property. Added the `?string` type.

protected static ?string $navigationLabel; // Protected the property. Added the `?string` type.

protected static ?string $slug; // Protected the property. Added the `?string` type.
```

### Theming changes

The theming system has entirely changed, to add support for Tailwind JIT's opacity features, which don't support static color codes.

Follow the instructions on the [appearance page](appearance#building-themes) to find out how to compile your own Filament stylesheet.
