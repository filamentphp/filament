---
title: Getting started
---
import LaracastsBanner from "@components/LaracastsBanner.astro"

## Overview

Panels are the top-level container in Filament, allowing you to build feature-rich admin panels that include pages, resources, forms, tables, notifications, actions, infolists, and widgets. All Panels include a default dashboard that can include widgets with statistics, charts, tables, and more.

<LaracastsBanner
    title="Introduction to Filament"
    description="Watch the Rapid Laravel Development with Filament series on Laracasts - it will teach you how to get started with the panel builder. Alternatively, continue reading this text-based guide."
    url="https://laracasts.com/series/rapid-laravel-development-with-filament/episodes/2"
    series="rapid-laravel-development"
/>

## Prerequisites

Before using Filament, you should be familiar with Laravel. Filament builds upon many core Laravel concepts, especially [database migrations](https://laravel.com/docs/migrations) and [Eloquent ORM](https://laravel.com/docs/eloquent). If you're new to Laravel or need a refresher, we highly recommend completing the [Laravel Bootcamp](https://bootcamp.laravel.com), which covers the fundamentals of building Laravel apps.

## The demo project

This guide covers building a simple patient management system for a veterinary practice using Filament. It will support adding new patients (cats, dogs, or rabbits), assigning them to an owner, and recording which treatments they received. The system will have a dashboard with statistics about the types of patients and a chart showing the number of treatments administered over the past year.

## Setting up the database and models

This project needs three models and migrations: `Owner`, `Patient`, and `Treatment`. Use the following artisan commands to create these:

```bash
php artisan make:model Owner -m
php artisan make:model Patient -m
php artisan make:model Treatment -m
```

### Defining migrations

Use the following basic schemas for your database migrations:

```php

// create_owners_table
Schema::create('owners', function (Blueprint $table) {
    $table->id();
    $table->string('email');
    $table->string('name');
    $table->string('phone');
    $table->timestamps();
});

// create_patients_table
Schema::create('patients', function (Blueprint $table) {
    $table->id();
    $table->date('date_of_birth');
    $table->string('name');
    $table->foreignId('owner_id')->constrained('owners')->cascadeOnDelete();
    $table->string('type');
    $table->timestamps();
});

// create_treatments_table
Schema::create('treatments', function (Blueprint $table) {
    $table->id();
    $table->string('description');
    $table->text('notes')->nullable();
    $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
    $table->unsignedInteger('price')->nullable();
    $table->timestamps();
});
```

Run the migrations using `php artisan migrate`.

### Unguarding all models

For brevity in this guide, we will disable Laravel's [mass assignment protection](https://laravel.com/docs/eloquent#mass-assignment). Filament only saves valid data to models so the models can be unguarded safely. To unguard all Laravel models at once, add `Model::unguard()` to the `boot()` method of `app/Providers/AppServiceProvider.php`:

```php
use Illuminate\Database\Eloquent\Model;

public function boot(): void
{
    Model::unguard();
}
```

### Setting up relationships between models

Let's set up relationships between the models. For our system, pet owners can own multiple pets (patients), and patients can have many treatments:

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

In Filament, resources are static classes used to build CRUD interfaces for your Eloquent models. They describe how administrators can interact with data from your panel using tables and forms.

Since patients (pets) are the core entity in this system, let's start by creating a patient resource that enables us to build pages for creating, viewing, updating, and deleting patients.

Use the following artisan command to create a new Filament resource for the `Patient` model:

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

Visit `/admin/patients` in your browser and observe a new link called "Patients" in the navigation. Clicking the link will display an empty table. Let's add a form to create new patients.

### Setting up the resource form

If you open the `PatientResource.php` file, there's a `form()` method with an empty `schema([...])` array. Adding form fields to this schema will build a form that can be used to create and edit new patients.

#### "Name" text input

Filament bundles a large selection of [form fields](../forms/fields/getting-started). Let's start with a simple [text input field](../forms/fields/text-input):

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

Visit `/admin/patients/create` (or click the "New Patient" button) and observe that a form field for the patient's name was added.

Since this field is required in the database and has a maximum length of 255 characters, let's add two [validation rules](../forms/validation) to the name field:

```php
use Filament\Forms;

Forms\Components\TextInput::make('name')
    ->required()
    ->maxLength(255)
```

Attempt to submit the form to create a new patient without a name and observe that a message is displayed informing you that the name field is required.

#### "Type" select

Let's add a second field for the type of patient: a choice between a cat, dog, or rabbit. Since there's a fixed set of options to choose from, a [select](../forms/fields/select) field works well:

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

The `options()` method of the Select field accepts an array of options for the user to choose from. The array keys should match the database, and the values are used as the form labels. Feel free to add as many animals to this array as you wish.

Since this field is also required in the database, let's add the `required()` validation rule:

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

Let's add a [date picker field](../forms/fields/date-time-picker) for the `date_of_birth` column along with the validation (the date of birth is required and the date should be no later than the current day).

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

#### "Owner" select

We should also add an owner when creating a new patient. Since we added a `BelongsTo` relationship in the Patient model (associating it to the related `Owner` model), we can use the **[`relationship()` method](../forms/fields/select#integrating-with-an-eloquent-relationship)** from the select field to load a list of owners to choose from:

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

The first argument of the `relationship()` method is the name of the function that defines the relationship in the model (used by Filament to load the select options) — in this case, `owner`. The second argument is the column name to use from the related table — in this case, `name`.

Let's also make the `owner` field required, `searchable()`, and `preload()` the first 50 owners into the searchable list (in case the list is long):

```php
use Filament\Forms;

Forms\Components\Select::make('owner_id')
    ->relationship('owner', 'name')
    ->searchable()
    ->preload()
    ->required()
```

#### Creating new owners without leaving the page
Currently, there are no owners in our database. Instead of creating a separate Filament owner resource, let's give users an easier way to add owners via a modal form (accessible as a `+` button next to the select). Use the [`createOptionForm()` method](../forms/fields/select#creating-a-new-option-in-a-modal) to embed a modal form with [`TextInput` fields](../forms/fields/text-input) for the owner's name, email address, and phone number:

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

A few new methods on the TextInput were used in this example:

- `label()` overrides the auto-generated label for each field. In this case, we want the `Email` label to be `Email address`, and the `Phone` label to be `Phone number`.
- `email()` ensures that only valid email addresses can be input into the field. It also changes the keyboard layout on mobile devices.
- `tel()` ensures that only valid phone numbers can be input into the field. It also changes the keyboard layout on mobile devices.

The form should be working now! Try creating a new patient and their owner. Once created, you will be redirected to the Edit page, where you can update their details.

### Setting up the patients table

Visit the `/admin/patients` page again. If you have created a patient, there should be one empty row in the table — with an edit button. Let's add some columns to the table, so we can view the actual patient data.

Open the `PatientResource.php` file. You should see a `table()` method with an empty `columns([...])` array. You can use this array to add columns to the `patients` table.

#### Adding text columns

Filament bundles a large selection of [table columns](../tables/columns). Let's use a simple [text column](../tables/columns/text) for all the fields in the `patients` table:

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

> Filament uses dot notation to eager-load related data. We used `owner.name` in our table to display a list of owner names instead of less informational ID numbers. You could also add columns for the owner's email address and phone number.

##### Making columns searchable

The ability to [search](/tables/columns/getting-started#searching) for patients directly in the table would be helpful as a veterinary practice grows. You can make columns searchable by chaining the `searchable()` method to the column. Let's make the patient's name and owner's name searchable.

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

Reload the page and observe a new search input field on the table that filters the table entries using the search criteria.

##### Making the columns sortable

To make the `patients` table [sortable](../tables/columns/getting-started#sorting) by age, add the `sortable()` method to the `date_of_birth` column:

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

This will add a sort icon button to the column header. Clicking it will sort the table by date of birth.

#### Filtering the table by patient type

Although you can make the `type` field searchable, making it filterable is a much better user experience.

Filament tables can have [filters](../tables/filters/getting-started), which are components that reduce the number of records in a table by adding a scope to the Eloquent query. Filters can even contain custom form components, making them a potent tool for building interfaces.

Filament includes a prebuilt [`SelectFilter`](../tables/filters/select) that you can add to the table's `filters()`:

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

Reload the page, and you should see a new filter icon in the top right corner (next to the search form). The filter opens a select menu with a list of patient types. Try filtering your patients by type.

## Introducing relation managers

Currently, patients can be associated with their owners in our system. But what happens if we want a third level? Patients come to the vet practice for treatment, and the system should be able to record these treatments and associate them with a patient.

One option is to create a new `TreatmentResource` with a select field to associate treatments with a patient. However, managing treatments separately from the rest of the patient information is cumbersome for the user. Filament uses "relation managers" to solve this problem.

Relation managers are tables that display related records for an existing resource on the edit screen for the parent resource. For example, in our project, you could view and manage a patient's treatments directly below their edit form.

> You can also use Filament ["actions"](../actions/modals#modal-forms) to open a modal form to create, edit, and delete treatments directly from the patient's table.

Use the `make:filament-relation-manager` artisan command to quickly create a relation manager, connecting the patient resource to the related treatments:

```bash
php artisan make:filament-relation-manager PatientResource treatments description
```

- `PatientResource` is the name of the resource class for the owner model. Since treatments belong to patients, the treatments should be displayed on the Edit Patient page.
- `treatments` is the name of the relationship in the Patient model we created earlier.
- `description` is the column to display from the treatments table.

This will create a `PatientResource/RelationManagers/TreatmentsRelationManager.php` file. You must register the new relation manager in the `getRelations()` method of the `PatientResource`:

```php
use App\Filament\Resources\PatientResource\RelationManagers;

public static function getRelations(): array
{
    return [
        RelationManagers\TreatmentsRelationManager::class,
    ];
}
```

The `TreatmentsRelationManager.php` file contains a class that is prepopulated with a form and table using the parameters from the `make:filament-relation-manager` artisan command. You can customize the fields and columns in the relation manager similar to how you would in a resource:

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

By default, text fields only span half the width of the form. Since the `description` field might contain a lot of information, add a `columnSpan('full')` method to make the field span the entire width of the modal form:

```php
use Filament\Forms;

Forms\Components\TextInput::make('description')
    ->required()
    ->maxLength(255)
    ->columnSpan('full')
```

Let's add the `notes` field, which can be used to add more details about the treatment. We can use a [textarea](../forms/fields/textarea) field with a `columnSpan('full')`:

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

Let's add a `price` field for the treatment. We can use a text input with some customizations to make it suitable for currency input. It should be `numeric()`, which adds validation and changes the keyboard layout on mobile devices. Add your preferred currency prefix using the `prefix()` method; for example, `prefix('€')` will add a `€` before the input without impacting the saved output value:

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
                ->prefix('€')
                ->maxValue(42949672.95),
        ]);
}
```

##### Casting the price to an integer

Filament stores currency values as integers (not floats) to avoid rounding and precision issues — a widely-accepted approach in the Laravel community. However, this requires creating a cast in Laravel that transforms the integer into a float when retrieved and back to an integer when stored in the database. Use the following artisan command to create the cast:

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

When the relation manager was generated previously, the `description` text column was automatically added. Let's also add a `sortable()` column for the `price` with a currency prefix. Use the Filament `money()` method to format the `price` column as money — in this case for `EUR` (`€`):

```php
use Filament\Tables;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('description'),
            Tables\Columns\TextColumn::make('price')
                ->money('EUR')
                ->sortable(),
        ]);
}
```

Let's also add a column to indicate when the treatment was administered using the default `created_at` timestamp. Use the `dateTime()` method to display the date-time in a human-readable format:

```php
use Filament\Tables;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('description'),
            Tables\Columns\TextColumn::make('price')
                ->money('EUR')
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime(),
        ]);
}
```

> You can pass any valid [PHP date formatting string](https://www.php.net/manual/en/datetime.format.php) to the `dateTime()` method (e.g. `dateTime('m-d-Y h:i A')`).

## Introducing widgets

Filament widgets are components that display information on your dashboard, especially statistics. Widgets are typically added to the default [Dashboard](../panels/dashboard) of the panel, but you can add them to any page, including resource pages. Filament includes built-in widgets like the [stats widget](../widgets/stats-overview), to render important statistics in a simple overview; [chart widget](../widgets/charts), which can render an interactive chart; and [table widget](../panels/dashboard#table-widgets), which allows you to easily embed the Table Builder.

Let's add a stats widget to our default dashboard page that includes a stat for each type of patient and a chart to visualize treatments administered over time.

### Creating a stats widget

Create a [stats widget](../widgets/stats-overview) to render patient types using the following artisan command:

```bash
php artisan make:filament-widget PatientTypeOverview --stats-overview
```

When prompted, do not specify a resource, and select "admin" for the location.

This will create a new `app/Filament/Widgets/PatientTypeOverview.php` file. Open it, and return `Stat` instances from the `getStats()` method:

```php
<?php

namespace App\Filament\Widgets;

use App\Models\Patient;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PatientTypeOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Cats', Patient::query()->where('type', 'cat')->count()),
            Stat::make('Dogs', Patient::query()->where('type', 'dog')->count()),
            Stat::make('Rabbits', Patient::query()->where('type', 'rabbit')->count()),
        ];
    }
}
```

Open your dashboard, and you should see your new widget displayed. Each stat should show the total number of patients for the specified type.

### Creating a chart widget

Let's add a chart to the dashboard to visualize the number of treatments administered over time. Use the following artisan command to create a new chart widget:

```bash
php artisan make:filament-widget TreatmentsChart --chart
```

When prompted, do not specify a resource, select "admin" for the location, and choose "line chart" as the chart type.

Open `app/Filament/Widgets/TreatmentsChart.php` and set the `$heading` of the chart to "Treatments".

The `getData()` method returns an array of datasets and labels. Each dataset is a labeled array of points to plot on the chart, and each label is a string. This structure is identical to the [Chart.js](https://www.chartjs.org/docs) library, which Filament uses to render charts.

To populate chart data from an Eloquent model, Filament recommends that you install the [flowframe/laravel-trend](https://github.com/Flowframe/laravel-trend) package:

```bash
composer require flowframe/laravel-trend
```

Update the `getData()` to display the number of treatments per month for the past year:

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

Now, check out your new chart widget in the dashboard!

> You can [customize your dashboard page](../panels/dashboard#customizing-the-dashboard-page) to change the grid and how many widgets are displayed.

## Next steps with the Panel Builder

Congratulations! Now that you know how to build a basic Filament application, here are some suggestions for further learning:

- [Create custom pages in the panel that don't belong to resources.](pages)
- [Learn more about adding action buttons to pages and resources, with modals to collect user input or for confirmation.](../actions/overview)
- [Explore the available fields to collect input from your users.](../forms/fields/getting-started#available-fields)
- [Check out the list of form layout components.](../forms/layout/getting-started)
- [Discover how to build complex, responsive table layouts without touching CSS.](../tables/layout)
- [Add summaries to your tables](../tables/summaries)
- [Write automated tests for your panel using our suite of helper methods.](testing)
