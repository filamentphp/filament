---
title: Export action
---

## Overview

Filament v3.2 introduced a prebuilt action that is able to export rows to a CSV or XLSX file. When the trigger button is clicked, a modal asks for the columns that they want to export, and what they should be labeled. This feature uses [job batches](https://laravel.com/docs/queues#job-batching) and [database notifications](../../notifications/database-notifications#overview), so you need to publish those migrations from Laravel. Also, you need to publish the migrations for tables that Filament uses to store information about exports:

```bash
# Laravel 11 and higher
php artisan make:queue-batches-table
php artisan make:notifications-table

# Laravel 10
php artisan queue:batches-table
php artisan notifications:table
```

```bash
# All apps
php artisan vendor:publish --tag=filament-actions-migrations
php artisan migrate
```

> If you're using PostgreSQL, make sure that the `data` column in the notifications migration is using `json()`: `$table->json('data')`.

> If you're using UUIDs for your `User` model, make sure that your `notifiable` column in the notifications migration is using `uuidMorphs()`: `$table->uuidMorphs('notifiable')`.

You may use the `ExportAction` like so:

```php
use App\Filament\Exports\ProductExporter;
use Filament\Actions\ExportAction;

ExportAction::make()
    ->exporter(ProductExporter::class)
```

If you want to add this action to the header of a table instead, you can use `Filament\Tables\Actions\ExportAction`:

```php
use App\Filament\Exports\ProductExporter;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->headerActions([
            ExportAction::make()
                ->exporter(ProductExporter::class)
        ]);
}
```

Or if you want to add it as a table bulk action, so that the user can choose which rows to export, they can use `Filament\Tables\Actions\ExportBulkAction`:

```php
use App\Filament\Exports\ProductExporter;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->bulkActions([
            ExportBulkAction::make()
                ->exporter(ProductExporter::class)
        ]);
}
```

The ["exporter" class needs to be created](#creating-an-exporter) to tell Filament how to export each row.

## Creating an exporter

To create an exporter class for a model, you may use the `make:filament-exporter` command, passing the name of a model:

```bash
php artisan make:filament-exporter Product
```

This will create a new class in the `app/Filament/Exports` directory. You now need to define the [columns](#defining-exporter-columns) that can be exported.

### Automatically generating exporter columns

If you'd like to save time, Filament can automatically generate the [columns](#defining-exporter-columns) for you, based on your model's database columns, using `--generate`:

```bash
php artisan make:filament-exporter Product --generate
```

## Defining exporter columns

To define the columns that can be exported, you need to override the `getColumns()` method on your exporter class, returning an array of `ExportColumn` objects:

```php
use Filament\Actions\Exports\ExportColumn;

public static function getColumns(): array
{
    return [
        ExportColumn::make('name'),
        ExportColumn::make('sku')
            ->label('SKU'),
        ExportColumn::make('price'),
    ];
}
```

### Customizing the label of an export column

The label for each column will be generated automatically from its name, but you can override it by calling the `label()` method:

```php
use Filament\Actions\Exports\ExportColumn;

ExportColumn::make('sku')
    ->label('SKU')
```

### Configuring the default column selection

By default, all columns will be selected when the user is asked which columns they would like to export. You can customize the default selection state for a column with the `enabledByDefault()` method:

```php
use Filament\Actions\Exports\ExportColumn;

ExportColumn::make('description')
    ->enabledByDefault(false)
```

### Disabling column selection

By default, user will be asked which columns they would like to export. You can disable this functionality using `columnMapping(false)`:

```php
use App\Filament\Exports\ProductExporter;

ExportAction::make()
    ->exporter(ProductExporter::class)
    ->columnMapping(false)
```

### Calculated export column state

Sometimes you need to calculate the state of a column, instead of directly reading it from a database column.

By passing a callback function to the `state()` method, you can customize the returned state for that column based on the `$record`:

```php
use App\Models\Order;
use Filament\Actions\Exports\ExportColumn;

ExportColumn::make('amount_including_vat')
    ->state(function (Order $record): float {
        return $record->amount * (1 + $record->vat_rate);
    })
```

### Formatting the value of an export column

You may instead pass a custom formatting callback to `formatStateUsing()`, which accepts the `$state` of the cell, and optionally the Eloquent `$record`:

```php
use Filament\Actions\Exports\ExportColumn;

ExportColumn::make('status')
    ->formatStateUsing(fn (string $state): string => __("statuses.{$state}"))
```

If there are [multiple values](#exporting-multiple-values-in-a-cell) in the column, the function will be called for each value.

#### Limiting text length

You may `limit()` the length of the cell's value:

```php
use Filament\Actions\Exports\ExportColumn;

ExportColumn::make('description')
    ->limit(50)
```

#### Limiting word count

You may limit the number of `words()` displayed in the cell:

```php
use Filament\Actions\Exports\ExportColumn;

ExportColumn::make('description')
    ->words(10)
```

#### Adding a prefix or suffix

You may add a `prefix()` or `suffix()` to the cell's value:

```php
use Filament\Actions\Exports\ExportColumn;

ExportColumn::make('domain')
    ->prefix('https://')
    ->suffix('.com')
```

### Exporting multiple values in a cell

By default, if there are multiple values in the column, they will be comma-separated. You may use the `listAsJson()` method to list them as a JSON array instead:

```php
use Filament\Actions\Exports\ExportColumn;

ExportColumn::make('tags')
    ->listAsJson()
```

### Displaying data from relationships

You may use "dot notation" to access columns within relationships. The name of the relationship comes first, followed by a period, followed by the name of the column to display:

```php
use Filament\Actions\Exports\ExportColumn;

ExportColumn::make('author.name')
```

### Counting relationships

If you wish to count the number of related records in a column, you may use the `counts()` method:

```php
use Filament\Actions\Exports\ExportColumn;

ExportColumn::make('users_count')->counts('users')
```

In this example, `users` is the name of the relationship to count from. The name of the column must be `users_count`, as this is the convention that [Laravel uses](https://laravel.com/docs/eloquent-relationships#counting-related-models) for storing the result.

If you'd like to scope the relationship before calculating, you can pass an array to the method, where the key is the relationship name and the value is the function to scope the Eloquent query with:

```php
use Filament\Actions\Exports\ExportColumn;
use Illuminate\Database\Eloquent\Builder;

ExportColumn::make('users_count')->counts([
    'users' => fn (Builder $query) => $query->where('is_active', true),
])
```

### Determining relationship existence

If you simply wish to indicate whether related records exist in a column, you may use the `exists()` method:

```php
use Filament\Actions\Exports\ExportColumn;

ExportColumn::make('users_exists')->exists('users')
```

In this example, `users` is the name of the relationship to check for existence. The name of the column must be `users_exists`, as this is the convention that [Laravel uses](https://laravel.com/docs/eloquent-relationships#other-aggregate-functions) for storing the result.

If you'd like to scope the relationship before calculating, you can pass an array to the method, where the key is the relationship name and the value is the function to scope the Eloquent query with:

```php
use Filament\Actions\Exports\ExportColumn;
use Illuminate\Database\Eloquent\Builder;

ExportColumn::make('users_exists')->exists([
    'users' => fn (Builder $query) => $query->where('is_active', true),
])
```

### Aggregating relationships

Filament provides several methods for aggregating a relationship field, including `avg()`, `max()`, `min()` and `sum()`. For instance, if you wish to show the average of a field on all related records in a column, you may use the `avg()` method:

```php
use Filament\Actions\Exports\ExportColumn;

ExportColumn::make('users_avg_age')->avg('users', 'age')
```

In this example, `users` is the name of the relationship, while `age` is the field that is being averaged. The name of the column must be `users_avg_age`, as this is the convention that [Laravel uses](https://laravel.com/docs/eloquent-relationships#other-aggregate-functions) for storing the result.

If you'd like to scope the relationship before calculating, you can pass an array to the method, where the key is the relationship name and the value is the function to scope the Eloquent query with:

```php
use Filament\Actions\Exports\ExportColumn;
use Illuminate\Database\Eloquent\Builder;

ExportColumn::make('users_avg_age')->avg([
    'users' => fn (Builder $query) => $query->where('is_active', true),
], 'age')
```

## Configuring the export formats

By default, the export action will allow the user to choose between both CSV and XLSX formats. You can use the `ExportFormat` enum to customize this, by passing an array of formats to the `formats()` method on the action:

```php
use App\Filament\Exports\ProductExporter;
use Filament\Actions\Exports\Enums\ExportFormat;

ExportAction::make()
    ->exporter(ProductExporter::class)
    ->formats([
        ExportFormat::Csv,
    ])
    // or
    ->formats([
        ExportFormat::Xlsx,
    ])
    // or
    ->formats([
        ExportFormat::Xlsx,
        ExportFormat::Csv,
    ])
```

Alternatively, you can override the `getFormats()` method on the exporter class, which will set the default formats for all actions that use that exporter:

```php
use Filament\Actions\Exports\Enums\ExportFormat;

public function getFormats(): array
{
    return [
        ExportFormat::Csv,
    ];
}
```

## Modifying the export query

By default, if you are using the `ExportAction` with a table, the action will use the table's currently filtered and sorted query to export the data. If you don't have a table, it will use the model's default query. To modify the query builder before exporting, you can use the `modifyQueryUsing()` method on the action:

```php
use App\Filament\Exports\ProductExporter;
use Illuminate\Database\Eloquent\Builder;

ExportAction::make()
    ->exporter(ProductExporter::class)
    ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true))
```

Alternatively, you can override the `modifyQuery()` method on the exporter class, which will modify the query for all actions that use that exporter:

```php
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;

public static function modifyQuery(Builder $query): Builder
{
    return $query->with([
        'purchasable' => fn (MorphTo $morphTo) => $morphTo->morphWith([
            ProductPurchase::class => ['product'],
            ServicePurchase::class => ['service'],
            Subscription::class => ['plan'],
        ]),
    ]);
}
```

## Configuring the export filesystem

### Customizing the storage disk

By default, exported files will be uploaded to the storage disk defined in the [configuration file](../installation#publishing-configuration). You can also set the `FILAMENT_FILESYSTEM_DISK` environment variable to change this.

If you want to use a different disk for a specific export, you can pass the disk name to the `disk()` method on the action:

```php
ExportAction::make()
    ->exporter(ProductExporter::class)
    ->fileDisk('s3')
```

Alternatively, you can override the `getFileDisk()` method on the exporter class, returning the name of the disk:

```php
public function getFileDisk(): string
{
    return 's3';
}
```

### Configuring the export file names

By default, exported files will have a name generated based on the ID and type of the export. You can also use the `fileName()` method on the action to customize the file name:

```php
use Filament\Actions\Exports\Models\Export;

ExportAction::make()
    ->exporter(ProductExporter::class)
    ->fileName(fn (Export $export): string => "products-{$export->getKey()}.csv")
```

Alternatively, you can override the `getFileName()` method on the exporter class, returning a string:

```php

use Filament\Actions\Exports\Models\Export;

public function getFileName(Export $export): string
{
    return "products-{$export->getKey()}.csv";
}
```

## Using export options

The export action can render extra form components that the user can interact with when exporting a CSV. This can be useful to allow the user to customize the behavior of the exporter. For instance, you might want a user to be able to choose the format of specific columns when exporting. To do this, you can return options form components from the `getOptionsFormComponents()` method on your exporter class:

```php
use Filament\Forms\Components\TextInput;

public static function getOptionsFormComponents(): array
{
    return [
        TextInput::make('descriptionLimit')
            ->label('Limit the length of the description column content')
            ->integer(),
    ];
}
```

Alternatively, you can pass a set of static options to the exporter through the `options()` method on the action:

```php
ExportAction::make()
    ->exporter(ProductExporter::class)
    ->options([
        'descriptionLimit' => 250,
    ])
```

Now, you can access the data from these options inside the exporter class, by injecting the `$options` argument into any closure function. For example, you might want to use it inside `formatStateUsing()` to [format a column's value](#formatting-the-value-of-an-export-column):

```php
use Filament\Actions\Exports\ExportColumn;

ExportColumn::make('description')
    ->formatStateUsing(function (string $state, array $options): string {
        return (string) str($state)->limit($options['descriptionLimit'] ?? 100);
    })
```

Alternatively, since the `$options` argument is passed to all closure functions, you can access it inside `limit()`:

```php
use Filament\Actions\Exports\ExportColumn;

ExportColumn::make('description')
    ->limit(fn (array $options): int => $options['descriptionLimit'] ?? 100)
```

## Using a custom user model

By default, the `exports` table has a `user_id` column. That column is constrained to the `users` table:

```php
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
```

In the `Export` model, the `user()` relationship is defined as a `BelongsTo` relationship to the `App\Models\User` model. If the `App\Models\User` model does not exist, or you want to use a different one, you can bind a new `Authenticatable` model to the container in a service provider's `register()` method:

```php
use App\Models\Admin;
use Illuminate\Contracts\Auth\Authenticatable;

$this->app->bind(Authenticatable::class, Admin::class);
```

If your authenticatable model uses a different table to `users`, you should pass that table name to `constrained()`:

```php
$table->foreignId('user_id')->constrained('admins')->cascadeOnDelete();
```

### Using a polymorphic user relationship

If you want to associate exports with multiple user models, you can use a polymorphic `MorphTo` relationship instead. To do this, you need to replace the `user_id` column in the `exports` table:

```php
$table->morphs('user');
```

Then, in a service provider's `boot()` method, you should call `Export::polymorphicUserRelationship()` to swap the `user()` relationship on the `Export` model to a `MorphTo` relationship:

```php
use Filament\Actions\Exports\Models\Export;

Export::polymorphicUserRelationship();
```

## Limiting the maximum number of rows that can be exported

To prevent server overload, you may wish to limit the maximum number of rows that can be exported from one CSV file. You can do this by calling the `maxRows()` method on the action:

```php
ExportAction::make()
    ->exporter(ProductExporter::class)
    ->maxRows(100000)
```

## Changing the export chunk size

Filament will chunk the CSV, and process each chunk in a different queued job. By default, chunks are 100 rows at a time. You can change this by calling the `chunkSize()` method on the action:

```php
ExportAction::make()
    ->exporter(ProductExporter::class)
    ->chunkSize(250)
```

If you are encountering memory or timeout issues when exporting large CSV files, you may wish to reduce the chunk size.

## Changing the CSV delimiter

The default delimiter for CSVs is the comma (`,`). If you want to export using a different delimiter, you may override the `getCsvDelimiter()` method on the exporter class, returning a new one:

```php
public static function getCsvDelimiter(): string
{
    return ';';
}
```

You can only specify a single character, otherwise an exception will be thrown.

## Styling XLSX cells

If you want to style the cells of the XLSX file, you may override the `getXlsxCellStyle()` method on the exporter class, returning an [OpenSpout `Style` object](https://github.com/openspout/openspout/blob/4.x/docs/documentation.md#styling):

```php
use OpenSpout\Common\Entity\Style\Style;

public function getXlsxCellStyle(): ?Style
{
    return (new Style())
        ->setFontSize(12)
        ->setFontName('Consolas');
}
```

If you want to use a different style for the header cells of the XLSX file only, you may override the `getXlsxHeaderCellStyle()` method on the exporter class, returning an [OpenSpout `Style` object](https://github.com/openspout/openspout/blob/4.x/docs/documentation.md#styling):

```php
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;

public function getXlsxHeaderCellStyle(): ?Style
{
    return (new Style())
        ->setFontBold()
        ->setFontItalic()
        ->setFontSize(14)
        ->setFontName('Consolas')
        ->setFontColor(Color::rgb(255, 255, 77))
        ->setBackgroundColor(Color::rgb(0, 0, 0))
        ->setCellAlignment(CellAlignment::CENTER)
        ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);
}
```

## Customizing the export job

The default job for processing exports is `Filament\Actions\Exports\Jobs\PrepareCsvExport`. If you want to extend this class and override any of its methods, you may replace the original class in the `register()` method of a service provider:

```php
use App\Jobs\PrepareCsvExport;
use Filament\Actions\Exports\Jobs\PrepareCsvExport as BasePrepareCsvExport;

$this->app->bind(BasePrepareCsvExport::class, PrepareCsvExport::class);
```

Or, you can pass the new job class to the `job()` method on the action, to customize the job for a specific export:

```php
use App\Jobs\PrepareCsvExport;

ExportAction::make()
    ->exporter(ProductExporter::class)
    ->job(PrepareCsvExport::class)
```

### Customizing the export queue and connection

By default, the export system will use the default queue and connection. If you'd like to customize the queue used for jobs of a certain exporter, you may override the `getJobQueue()` method in your exporter class:

```php
public function getJobQueue(): ?string
{
    return 'exports';
}
```

You can also customize the connection used for jobs of a certain exporter, by overriding the `getJobConnection()` method in your exporter class:

```php
public function getJobConnection(): ?string
{
    return 'sqs';
}
```

### Customizing the export job middleware

By default, the export system will only process one job at a time from each export. This is to prevent the server from being overloaded, and other jobs from being delayed by large exports. That functionality is defined in the `WithoutOverlapping` middleware on the exporter class:

```php
public function getJobMiddleware(): array
{
    return [
        (new WithoutOverlapping("export{$this->export->getKey()}"))->expireAfter(600),
    ];
}
```

If you'd like to customize the middleware that is applied to jobs of a certain exporter, you may override this method in your exporter class. You can read more about job middleware in the [Laravel docs](https://laravel.com/docs/queues#job-middleware).

### Customizing the export job retries

By default, the export system will retry a job for 24 hours. This is to allow for temporary issues, such as the database being unavailable, to be resolved. That functionality is defined in the `getJobRetryUntil()` method on the exporter class:

```php
use Carbon\CarbonInterface;

public function getJobRetryUntil(): ?CarbonInterface
{
    return now()->addDay();
}
```

If you'd like to customize the retry time for jobs of a certain exporter, you may override this method in your exporter class. You can read more about job retries in the [Laravel docs](https://laravel.com/docs/queues#time-based-attempts).

### Customizing the export job tags

By default, the export system will tag each job with the ID of the export. This is to allow you to easily find all jobs related to a certain export. That functionality is defined in the `getJobTags()` method on the exporter class:

```php
public function getJobTags(): array
{
    return ["export{$this->export->getKey()}"];
}
```

If you'd like to customize the tags that are applied to jobs of a certain exporter, you may override this method in your exporter class.

### Customizing the export job batch name

By default, the export system doesn't define any name for the job batches. If you'd like to customize the name that is applied to job batches of a certain exporter, you may override the `getJobBatchName()` method in your exporter class:

```php
public function getJobBatchName(): ?string
{
    return 'product-export';
}
```

## Authorization

By default, only the user who started the export may download files that get generated. If you'd like to customize the authorization logic, you may create an `ExportPolicy` class, and [register it in your `AuthServiceProvider`](https://laravel.com/docs/10.x/authorization#registering-policies):

```php
use App\Policies\ExportPolicy;
use Filament\Actions\Exports\Models\Export;

protected $policies = [
    Export::class => ExportPolicy::class,
];
```

The `view()` method of the policy will be used to authorize access to the downloads.

Please note that if you define a policy, the existing logic of ensuring only the user who started the export can access it will be removed. You will need to add that logic to your policy if you want to keep it:

```php
use App\Models\User;
use Filament\Actions\Exports\Models\Export;

public function view(User $user, Export $export): bool
{
    return $export->user()->is($user);
}
```
