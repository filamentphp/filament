---
title: Getting started
---

## Overview

You may be wondering how to get started building a panel with Filament. There are many features, spread across several packages, that compose even the simplest of apps. This guide will teach you a few core concepts of the framework, and give you a glimpse of what is possible.

Before using Filament, you need to be familiar with Laravel. Many core Laravel concepts are used within Filament, especially [database migrations](https://laravel.com/docs/migrations) and [Eloquent ORM](https://laravel.com/docs/eloquent). If you've never used Laravel before, or need a refresher, we highly recommend that you follow the [Laravel Bootcamp](https://bootcamp.laravel.com/blade/installation) to build a small app. The guide will give you a great foundation of knowledge that you would otherwise be missing, and you will find Filament much easier and faster to understand and use.

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
    $table->text('notes')->nullable();
    $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
    $table->unsignedInteger('price')->nullable();
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

## Introducing resources

In Filament, resources are static classes that are used to build CRUD interfaces for your Eloquent models. They describe how administrators should be able to interact with data from your panel - using tables and forms.

In our vet management panel, patients are at the core of all operations. Patients should be listed in a table, and clicking on a patient should open a new page where their details are editable. Patients should be able to be deleted from this page too. Above the patient list, there should be a button to allow the user to add a new patient to the system.

We need to create a new resource for the `Patient` model:

```bash
php artisan make:filament-resource Patient
```

This will create several files in the `app/Filament/Resources` directory:

```
.
+-- PatientResource.php
+-- PatientResource
|   +-- Pages
|   |   +-- CreatePatient.php
|   |   +-- EditPatient.php
|   |   +-- ListPatients.php
```

Visit `/admin/patients` in your browser and observe that a new page has been added to the sidebar - "Patients".

### Setting up the resource form

If you open the `PatientResource.php` file, you can see a `form()` method with an empty `schema([...])` array. You can add form fields to this schema, which will then be used when you add new patients to the system, or edit them.

#### "Name" text input

Filament contains a [large selection of form fields](../forms/fields) built-in. The most simple field to start with is the [text input](../forms/fields/text-input):

```php
use Filament\Forms;
use Filament\Forms\Form;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name'),
        ]);
}
```

Visit `/admin/patients/create` (or click the "New patient" button), and observe that a form field for the patient's name has been added.

This field is required in the database, and has a maximum length of 255 characters. We can add [validation rules](../forms/validation) to the field to ensure that these constraints are met when the user submits the form:

```php
use Filament\Forms;

Forms\Components\TextInput::make('name')
    ->required()
    ->maxLength(255)
```

Attempt to submit the form to create a new patient without a name, and observe that a message is displayed informing you that the name field is required.

#### "Type" select

Let's add a second field, for the type of patient - either a cat, dog or rabbit. Since there are a set of options that should be chosen from, it's best to use a [select](../forms/fields/select) field for this:

```php
use Filament\Forms;
use Filament\Forms\Form;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('type')
                ->options([
                    'cat' => 'Cat',
                    'dog' => 'Dog',
                    'rabbit' => 'Rabbit',
                ]),
        ]);
}
```

The `options()` method of the Select field provides an array of options that the user may select from. The keys of the array are stored in the database, and the values are used as the label of each option in the app. You can be creative and add as many types of animals to this array as you wish.

Again, this field is also required in the database, so we must add the `required()` validation rule method:

```php
use Filament\Forms;

Forms\Components\Select::make('type')
    ->options([
        'cat' => 'Cat',
        'dog' => 'Dog',
        'rabbit' => 'Rabbit',
    ])
    ->required()
```

#### "Date of birth" picker

Since we have a `date_of_birth` column in our database, we will need to also add a field for that. A [date picker](../forms/fields/date-time-picker) is suitable:

```php
use Filament\Forms;
use Filament\Forms\Form;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('type')
                ->options([
                    'cat' => 'Cat',
                    'dog' => 'Dog',
                    'rabbit' => 'Rabbit',
                ])
                ->required(),
            Forms\Components\DatePicker::make('date_of_birth')
                ->required()
                ->maxDate(now()),
        ]);
}
```

The field is required, and the date of birth cannot be in the future, so we can set the max date to be today.

#### "Owner" select

When creating a pet, it'll be important to store its owner. This is already set up as a `BelongsTo` relationship, and we can easily load options from the `owners` table by using the [`relationship()` method of the select](../forms/fields/select#integrating-with-an-eloquent-relationship):

```php
use Filament\Forms;
use Filament\Forms\Form;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('type')
                ->options([
                    'cat' => 'Cat',
                    'dog' => 'Dog',
                    'rabbit' => 'Rabbit',
                ])
                ->required(),
            Forms\Components\DatePicker::make('date_of_birth')
                ->required()
                ->maxDate(now()),
            Forms\Components\Select::make('owner_id')
                ->relationship('owner', 'name')
                ->required(),
        ]);
}
```

The first argument of the `relationship()` method is the name of the relationship to load options from, and the second argument is the name of a column on the `owners` table to identify owners by. In our case, we want a list of owner's names to select from. The field is also required, as patients must be associated with an owner.

Owners should also be `searchable()`, as the list might be quite long, and we can `preload()` the first 50 owners into this searchable list:

```php
use Filament\Forms;

Forms\Components\Select::make('owner_id')
    ->relationship('owner', 'name')
    ->searchable()
    ->preload()
    ->required()
```

##### Creating new owners without leaving the page

At the moment, there aren't any owners in our database, and we need an easy way to add them. Also, patients might need to sign up quickly when the owner doesn't exist yet, and navigating to a separate `OwnerResource` just to do this would definitely slow the process down. Luckily, you can allow owners to be quickly created by clicking on a `+` button next to the select. This button will open a modal that contains a separate form to collect the owner's name, email address, and phone number. We can add these form fields inside the `createOptionForm()` method:

```php
use Filament\Forms;

Forms\Components\Select::make('owner_id')
    ->relationship('owner', 'name')
    ->searchable()
    ->preload()
    ->createOptionForm([
        Forms\Components\TextInput::make('name')
            ->required()
            ->maxLength(255),
        Forms\Components\TextInput::make('email')
            ->label('Email address')
            ->email()
            ->required()
            ->maxLength(255),
        Forms\Components\TextInput::make('phone')
            ->label('Phone number')
            ->tel()
            ->required(),
    ])
    ->required()
```

A few new methods on the text input were used in this example:

- `label()` overrides the auto-generated label for each field. In this case, we want the `Email` label to be `Email address` instead, and the `Phone` label to be `Phone number`.
- `email()` validates that only emails may be input into the field. It also changes the keyboard layout on mobile devices.
- `tel()` validates that only phone numbers may be input into the field. It also changes the keyboard layout on mobile devices.

The form should be working now! Go ahead and create a new patient, and their owner. Once they've been created, you will be redirected to the Edit page, where you can update their details.

### Setting up the patients table

Visit the `/admin/patients` page again. If you've created a patient, there should now be a row in the table. Unfortunately, there's no information in the table yet, only a button to edit the patient. This is because we haven't configured any table columns yet.

If you open the `PatientResource.php` file, you can see a `table()` method with an empty `columns([...])` array. You can add table columns to this schema, which will then be used when you list the patients.

#### Adding text columns

Filament contains a [selection of table columns](../tables/columns) built-in. The most common column type is the [text column](../tables/columns/text). All of our fields can be added as text columns in our table:

```php
use Filament\Tables;
use Filament\Tables\Table;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('type'),
            Tables\Columns\TextColumn::make('date_of_birth'),
            Tables\Columns\TextColumn::make('owner.name'),
        ]);
}
```

Note that for the owner column, we don't use `owner_id` as the text column name. Instead, we use `owner.name`. This dot notation is used by Filament to eager-load the results of that relationship, and render a list of owner names, instead of their IDs. You could add additional columns for the owner's email address and phone number if you wish.

##### Making columns searchable

[Searching](columns/getting-started#searching) through patients in this table would be useful. Patient results should be found by searching for their name, or their owner's name. You can make these columns `searchable()`:

```php
use Filament\Tables;
use Filament\Tables\Table;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->searchable(),
            Tables\Columns\TextColumn::make('type'),
            Tables\Columns\TextColumn::make('date_of_birth'),
            Tables\Columns\TextColumn::make('owner.name')
                ->searchable(),
        ]);
}
```

Now, there will be a search input in the table, and you will be able to filter rows by the value of that column.

##### Making the columns sortable

Patients could be [sorted](columns/getting-started#sorting) by their age, by making the `date_of_birth` column `sortable()`:

```php
use Filament\Tables;
use Filament\Tables\Table;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->searchable(),
            Tables\Columns\TextColumn::make('type'),
            Tables\Columns\TextColumn::make('date_of_birth')
                ->sortable(),
            Tables\Columns\TextColumn::make('owner.name')
                ->searchable(),
        ]);
}
```

This will add a sort button to the column header, and clicking it will sort the table by that column.

#### Filtering the table by patient type

The `type` field could be searchable, but it's probably much better UX to add a select to filter this column with.

Filament tables can have [filters](../tables/filters), which are components that allow you to scope the Eloquent query as a way to reduce the number of records in a table. Filters can even contain custom form components, which make them incredibly powerful to build interfaces with. While you could use a custom filter to render a select field, Filament includes a prebuilt [`SelectFilter`](../tables/filters#select-filters) that you can add to the `filters()` of the table:

```php
use Filament\Tables;
use Filament\Tables\Table;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('type')
                ->options([
                    'cat' => 'Cat',
                    'dog' => 'Dog',
                    'rabbit' => 'Rabbit',
                ]),
        ]);
}
```

Now, visit the patients list again. In the top right corner of the table, a new filter icon will open a dropdown which contains your filters. Try filtering your patients by type!

## Introducing relation managers

At the moment, patients can be associated with their owners. This is as far as we've gone so far with managing relationships with Filament. But what happens if we want a third level? Patients come to the vet practice for treatment, and the system should be able to record these treatments and associate them with a patient.

We could create a new `TreatmentResource`, with a select field to associate a treatment with a patient. But the UX of having treatments in a separate place to the rest of the patient information probably isn't that great.

Filament has a concept called "relation managers". These are tables which you can add to any existing resource, and get rendered on the Edit page. The table will list the records related to the "owner" (the resource you're editing). In our project, you could list a patient's treatments below the patient's edit form.

Tables in Filament are also very powerful, as they can open modals from any table row, or by clicking on a button in the header of the table. These are called "actions". Using an action with a modal, you can create, edit, and delete treatments without leaving the patient's page.

To create a relation manager, you can use the `make:filament-relation-manager` command:

```bash
php artisan make:filament-relation-manager PatientResource treatments description
```

- `PatientResource` is the name of the resource class for the owner model. Treatments belong to patients, and the treatments relation manager should be rendered on the Edit Patient page.
- `treatments` is the name of the relationship we will manage.
- `description` is the column that will be used to identify treatments from each other in the table.

This will create a `PatientResource/RelationManagers/TreatmentsRelationManager.php` file. You must register the new relation manager in the `PatientResource`'s `getRelations()` method:

```php
use App\Filament\PatientResource\RelationManagers;

public static function getRelations(): array
{
    return [
        RelationManagers\TreatmentsRelationManager::class,
    ];
}
```

`TreatmentsRelationManager.php` contains a class where you are able to define a form and table for your relation manager, very similar to a resource:

```php
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

public function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('description')
                ->required()
                ->maxLength(255),
        ]);
}

public function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('description'),
        ]);
}
```

Visit the Edit page for one of your patients. You should already be able to create, edit, delete, and list treatments for that patient.

### Setting up the treatment form

Firstly, the `description` field could contain lots of information. We could make that field span the full-width of the modal instead of only half. You can use the `columnSpan('full')` method to do this:

```php
use Filament\Forms;

Forms\Components\TextInput::make('description')
    ->required()
    ->maxLength(255)
    ->columnSpan('full')
```

Let's add a `notes` field, which can be used to add more information about the treatment. We can use a [textarea](../forms/fields/textarea) for this:

```php
use Filament\Forms;
use Filament\Forms\Form;

public function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('description')
                ->required()
                ->maxLength(255)
                ->columnSpan('full'),
            Forms\Components\Textarea::make('notes')
                ->maxLength(65535)
                ->columnSpan('full'),
        ]);
}
```

#### Configuring the `price` field

Additionally, we need a `price` field. We can use a text input for this, with some customizations to make it suitable for currency input. It should be `numeric()`, which adds validation as well as changing the keyboard layout on mobile devices. We can also add a `prefix('$')` which will render a `$` before the input, while not changing the saved output of the field:

```php
use Filament\Forms;
use Filament\Forms\Form;

public function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('description')
                ->required()
                ->maxLength(255)
                ->columnSpan('full'),
            Forms\Components\Textarea::make('notes')
                ->maxLength(65535)
                ->columnSpan('full'),
            Forms\Components\TextInput::make('price')
                ->numeric()
                ->prefix('$')
                ->maxValue(42949672.95),
        ]);
}
```

##### Casting the price to an integer

Eagle-eyed readers will have noticed that our prices are being stored in the database as an integer, not a float (decimal). This is the widely-accepted way of storing money in the Laravel community. It requires us to create a cast for this attribute, which will transform the float into an integer, and back, so that it can be stored in the database. Create the cast:

```bash
php artisan make:cast MoneyCast
```

Inside the new `app/Casts/MoneyCast.php` file, update the `get()` and `set()` methods:

```php
public function get($model, string $key, $value, array $attributes): float
{
    // Transform the integer stored in the database into a float.
    return round(floatval($value) / 100, precision: 2);
}

public function set($model, string $key, $value, array $attributes): float
{
    // Transform the float into an integer for storage.
    return round(floatval($value) * 100);
}
```

Now, add the `MoneyCast` to the `price` attribute in the `Treatment` model:

```php
use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    protected $casts = [
        'price' => MoneyCast::class,
    ];

    // ...
}
```

### Setting up the treatments table

When the relation manager was generated, the `description` text column was already added.

Let's also add a column for the `price`. It should be `sortable()`, and formatted as money with a `$` prefix. Luckily, Filament has a `money()` method for formatting a text column as money - in this case for `usd` (`$`):

```php
use Filament\Tables;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('description'),
            Tables\Columns\TextColumn::make('price')
                ->money('usd')
                ->sortable(),
        ]);
}
```

Also, we can add a column to indicate when the treatment was administered. We can use the default `created_at` timestamp for this, but you could add another database column to store this information if you wanted. We can format the text column in a human-readable date-time format using the `dateTime()` method:

```php
use Filament\Tables;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('description'),
            Tables\Columns\TextColumn::make('price')
                ->money('usd')
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime(),
        ]);
}
```

## Introducing widgets

Filament has "widgets" - which are components that you can use to display information, especially statistics. Widgets typically get added to the Dashboard of the panel, but you can add them to any page you wish, including resource pages. Filament includes built-in widgets, like the [stats widget](../stats-overview) to render important statistics in a simple card, [chart widget](../widgets/charts) which can render an interactive chart, and [table widget](../panels/dashboard#table-widgets) which allows you to easily embed the table builder.

In our system, we could add statistics for the type of patient, as well as treatments that are administered over time.

### Creating a stats widget

Let's create a [stats widget](../widgets/stats-overview) to render patient types:

```bash
php artisan make:filament-widget PatientTypeOverview --stats-overview
```

This will generate a `app/Filament/Widgets/PatientTypeOverview.php` file. Open it, and return `Card` instances from the `getCards()` method:

```php
<?php

namespace App\Filament\Widgets;

use App\Models\Patient;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class PatientTypeOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Cats', Patient::query()->where('type', 'cat')->count()),
            Card::make('Dogs', Patient::query()->where('type', 'dog')->count()),
            Card::make('Rabbits', Patient::query()->where('type', 'rabbit')->count()),
        ];
    }
}
```

Now, check out your widget in the dashboard. Each card will contain the number of that type of patient.

### Creating a chart widget

We can add a chart to our dashboard to represent treatments that are administered over time. Start by creating a widget with the command:

```bash
php artisan make:filament-widget TreatmentsChart --chart
```

When prompted for the chart type, choose the "line chart".

You can set the `$heading` of the chart to `'Treatments'`.

The `getData()` method is used to return an array of datasets and labels. Each dataset is a labeled array of points to plot on the chart, and each label is a string. This structure is identical with the [Chart.js](https://www.chartjs.org/docs) library, which Filament uses to render charts.

To generate chart data from an Eloquent model, Filament recommends that you install the `flowframe/laravel-trend` package. You can view the [documentation](https://github.com/Flowframe/laravel-trend). Install the package:

```bash
composer require flowframe/laravel-trend
```

Now, you can generate the number of treatments per month for this year:

```php
use App\Models\Treatment;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

protected function getData(): array
{
    $data = Trend::model(Treatment::class)
        ->between(
            start: now()->subYear(),
            end: now(),
        )
        ->perMonth()
        ->count();

    return [
        'datasets' => [
            [
                'label' => 'Treatments',
                'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
            ],
        ],
        'labels' => $data->map(fn (TrendValue $value) => $value->date),
    ];
}
```

Now, check out your widget in the dashboard.

## Next steps with the panel builder

Now you've finished reading this guide, where to next? Here are some suggestions:

- [Create custom pages in the panel that don't belong to resources.](pages)
- [Learn more about adding action buttons to pages and resources, with modals to get user input or confirmation.](../actions/overview)
- [Explore the available fields to collect input from your users.](../forms/fields/getting-started#available-fields)
- [Check out the list of layout components to craft intuitive form structures with.](../forms/fields/getting-started#available-fields)
- [Discover how to build complex, responsive table layouts without touching CSS.](../tables/layout)
- [Add summaries to your tables, which give an overview of the data inside of them.](../tables/summaries)
- [Write automated tests for your panel using our suite of helper methods.](testing)
