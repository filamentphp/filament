---
title: Getting Started
---

Resources are static classes that describe how administrators should be able to interact with data from your app. They are associated with Eloquent models from your app.

To create a resource for the `App\Models\Customer` model:

```bash
php artisan make:filament-resource Customer
```

This will create several files in the `app/Filament/Resources` directory:

```
.
+-- CustomerResource.php
+-- CustomerResource
|   +-- Pages
|   |   +-- CreateCustomer.php
|   |   +-- EditCustomer.php
|   |   +-- ListCustomers.php
```

Your new resource class lives in `CustomerResource.php`. Resource classes register [forms](#forms), [tables](#tables), [relations](#relations), and [pages](#pages) associated with that model.

The classes in the `Pages` directory are used to customize the pages in the admin panel that interact with your resource. They're all full-page [Livewire](https://laravel-livewire.com) components that you can customize in any way you wish.

### Setting a title attribute

A `$recordTitleAttribute` may be set for your resource, which is the name of a column on your model that can be used to identify it from others.

This is required for features like [global search](#global-search) to work.

For example, this could be a blog post's `title` or a customer's `name`.

```php
protected static ?string $recordTitleAttribute = 'name';
```

> You may specify the name of an [Eloquent accessor](https://laravel.com/docs/eloquent-mutators#defining-an-accessor) if just one column is unable to describe a record effectively.
