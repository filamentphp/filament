---
title: Import action
---

## Overview

Filament includes a prebuilt action that is able to import rows from a CSV. When the trigger button is clicked, a modal asks the user for a file. Once they upload one, they are able to map each column in the CSV to a real column in the database. They can also download an example CSV file containing all the columns that can be imported. You may use it like so:

```php
use App\Filament\Imports\ProductImporter;
use Filament\Actions\ImportAction;

ImportAction::make()
    ->importer(ProductImporter::class)
```

The "importer" class needs to be created to tell Filament how to import each row of the CSV.

## Creating an importer

