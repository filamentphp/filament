![Filament User Listing Screenshot](https://raw.githubusercontent.com/laravel-filament/filament/master/resources/images/screenshots/edit-user-darkmode.jpg)

# Filament

A lightweight admin for your Laravel app.

#### **_This package is in very active development – I would love help to make it awesome!_**

## Notes

### Keep it Minimal

The theme of this admin is "Light and Fast" – just enough to get you started and creating your next project – keeping setup to a minimum with hardly any changes to the default Laravel app. Let's use as little Javascript as possible by adhereing to using [Livewire](https://laravel-livewire.com) as much as possible (using [Alpine JS](https://github.com/alpinejs/alpine) when absolutely needed and use [Spruce](https://github.com/ryangjchandler/spruce) for some simple state management between front-end components when absolutely necessary).

### Roadmap

> This is more of a proposed feature list then a solid roadmap. I am _totally open_ to new ideas, with the overall goal of keeping this core package pretty minimal allowing total customization via your main Laravel app / additional add-on packages.

- Setup tests (via [Testbench Component](https://github.com/orchestral/testbench)) and start creating them for existing features.
  - _I'm going to be honest, I don't know much about testing within a package, so this will be completely new to me and I would really appreciate some help on getting things setup for best practices etc.).
- Create new `Globals` feature for managing arbitrary app data.
  - Considering implementing [laravel-metable](https://github.com/plank/laravel-metable).
- Create new `Resources` feature. Allowing a user to create a generic resource with schemaless attributes managed by custom fields.

  - Considering implementing [laravel-metable](https://github.com/plank/laravel-metable) in this feature as well, as it would allow custom fields in a pretty flexible way.

---

## Installation

This package can be used with `Laravel 7.x` or higher.

> The following instructions assume a new installation of Laravel with database and mail setup.

```bash
composer require filament/filament dev-master
php artisan migrate
php artisan vendor:publish --tag=filament-seeds
composer dump-autoload
php artisan db:seed --class=FilamentSeeder
```

Add the necessary `Filament\Traits\FilamentUser` trait to your `App\User` model.

---

## Create a user

```bash
php artisan filament:user
```

## Create a fieldset

> This feature will eventually allow you to create a fieldset in your own app for `Globals` or `Resources`. More documentation to come as these features are added to the core package.

```bash
php artisan filament:fieldset {name} {--package}
```

---

### Screenshots

- [Login](https://raw.githubusercontent.com/laravel-filament/filament/master/resources/images/screenshots/login-darkmode.jpg)
- [Users](https://raw.githubusercontent.com/laravel-filament/filament/master/resources/images/screenshots/users-lightmode.jpg)
- [Edit user](https://raw.githubusercontent.com/laravel-filament/filament/master/resources/images/screenshots/edit-user-darkmode.jpg)
- [Roles](https://raw.githubusercontent.com/laravel-filament/filament/master/resources/images/screenshots/roles-darkmode.jpg)
- [Permissions](https://raw.githubusercontent.com/laravel-filament/filament/master/resources/images/screenshots/permissions-darkmode.jpg)
- [Edit permission](https://raw.githubusercontent.com/laravel-filament/filament/master/resources/images/screenshots/edit-permission-darkmode.jpg)
