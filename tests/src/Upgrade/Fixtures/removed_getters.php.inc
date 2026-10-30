<?php

namespace RemovedGetters;

use Filament\Actions\Imports\ImportColumn;
use Filament\Schemas\Components\Component;

function inspect(ImportColumn $column, ?ImportColumn $nullable, Component $component, object $unrelated): void
{
    $column->getExample();
    $column->examples(['key' => 'first'])->getExample();
    $nullable?->getExample();
    $component->isConcealed();
    $component?->isConcealed();
    $unrelated->getExample();
    $unrelated->isConcealed();
    $column->getExample('extra');
    $callback = $column->getExample(...);
}
-----
<?php

namespace RemovedGetters;

use Illuminate\Support\Arr;
use Filament\Actions\Imports\ImportColumn;
use Filament\Schemas\Components\Component;

function inspect(ImportColumn $column, ?ImportColumn $nullable, Component $component, object $unrelated): void
{
    Arr::first($column->getExamples());
    Arr::first($column->examples(['key' => 'first'])->getExamples());
    Arr::first($nullable?->getExamples() ?? []);
    false;
    $component === null ? null : false;
    $unrelated->getExample();
    $unrelated->isConcealed();
    $column->getExample('extra');
    $callback = $column->getExample(...);
}
