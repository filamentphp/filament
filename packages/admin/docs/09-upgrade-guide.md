---
title: Upgrading from v1.x
---

> Due to the nature of these changes, we recommend that you make them before you upgrade the Filament package to v2.x. This will prevent errors from occurring during the Composer upgrade process due to undefined classes and missing types.

## High impact changes

### Configuration file

If you've published the v1.x `filament.php` configuration file, you should run the following command to overwrite it:

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
<code>database/migrations/0000_00_00_000000_create_filament_users_table.php</code>
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
</details>
