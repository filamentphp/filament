---
title: Replicate action
---

## Overview

Filament includes a prebuilt action that is able to [replicate](https://laravel.com/docs/eloquent#replicating-models) Eloquent records. You may use it like so:

```php
use Filament\Actions\ReplicateAction;

ReplicateAction::make()
    ->record($this->post)
```

If you want to replicate table rows, you can use the `Filament\Tables\Actions\ReplicateAction` instead:

```php
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->actions([
            ReplicateAction::make(),
            // ...
        ]);
}
```

## Excluding attributes

The `excludeAttributes()` method is used to instruct the action which columns to be excluded from replication:

```php
ReplicateAction::make()
    ->excludeAttributes(['slug'])
```

## Lifecycle hooks

You can use the `beforeReplicaSaved()` and `afterReplicaSaved()` methods to execute code before and after a replica is saved:

```php
use Illuminate\Database\Eloquent\Model;

ReplicateAction::make()
    ->beforeReplicaSaved(function (Model $replica): void {
        // ...
    })
    ->afterReplicaSaved(function (Model $replica): void {
        // ...
    })
```