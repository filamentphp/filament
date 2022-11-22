---
title: Getting started
---

You may be wondering how to get started building an app with Filament. There are many features, spread across several packages, that compose even the simplest of admin panels. This guide will teach you a few core concepts of the framework, and give you a glimpse of what is possible.

Before using Filament, you need to be familiar with Laravel. Many core Laravel concepts are used within Filament, especially [database migrations](https://laravel.com/docs/migrations) [Eloquent ORM](https://laravel.com/docs/eloquent). If you've never used Laravel before, or need a refresher, we highly recommend that you follow the [Laravel Bootcamp](https://bootcamp.laravel.com/blade/installation) to build a small app. The guide will give you a great foundation of knowledge that you would otherwise be missing, and you will find Filament much easier and faster to understand and use.

In this guide, we'll be building a small patient management system for a vet practice. Vets will be able to add new patients (cats, dogs, or rabbits) to the system, assign them to an owner, and record which treatments they have received at the practice.

## Setting up the database and models

We'll be needing 3 models and migrations for this project - `Owner`, `Patient` and `Treatment`:

```bash
php artisan make:model Owner -m
php artisan make:model Patient -m
php artisan make:model Treatment -m
```

Again, if you're not familiar with this process, please follow the [Laravel Bootcamp](https://bootcamp.laravel.com/blade/installation).

### Defining migrations

Our database migrations will be simple in this guide:

```php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

Schema::create('owners', function (Blueprint $table) {
    $table->id();
    $table->string('email');
    $table->string('name');
    $table->string('phone');
    $table->timestamps();
});

Schema::create('patients', function (Blueprint $table) {
    $table->id();
    $table->date('date_of_birth');
    $table->string('name');
    $table->foreignId('owner_id')->constrained('owners')->cascadeOnDelete();
    $table->string('type');
    $table->timestamps();
});

Schema::create('treatments', function (Blueprint $table) {
    $table->id();
    $table->string('description');
    $table->text('notes');
    $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
    $table->unsignedInteger('price');
    $table->timestamps();
});
```

### Unguarding all models

For brevity in this guide, we're going to disable Laravel's [mass assignment protection](https://laravel.com/docs/eloquent#mass-assignment). Filament takes mass assignment precautions by only saving valid data, so models can be unguarded without issue. To unguard all models at once, simply add `Model::unguard()` to the `boot()` method of `app/Providers/AppServiceProvider.php`:

```php
use Illuminate\Database\Eloquent\Model;

public function boot(): void
{
    Model::unguard();
}
```

### Setting up relationships between models

Let's set up relationships between the models. Pet owners can own multiple pets (patients), and patients can have many treatments:

```php
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Owner extends Model
{
    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }
}

class Patient extends Model
{
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function treatments(): HasMany
    {
        return $this->hasMany(Treatment::class);
    }
}

class Treatment extends Model
{
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
```

- models & migrations
- resource
- add table
- add form
  - select with createoptionform
- add relation manager

- dashboard widgets
