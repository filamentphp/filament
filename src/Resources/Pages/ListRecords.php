<?php

namespace Filament\Resources\Pages;

use Filament\Components\Concerns;
use Filament\Resources\Page;
use Filament\Tables\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ListRecords extends Page
{
    use HasTable;

    public static $createButtonLabel = 'Create';

    protected static $view = 'filament::resources.pages.list-records';

    public $filterable = true;

    public $createRoute = 'create';

    public $pagination = true;

    public $recordRoute = 'edit';

    public $searchable = true;

    public $sortable = true;

    public static function getTitle()
    {
        if (property_exists(static::class, 'title')) return static::$title;

        return (string) Str::of(class_basename(static::getModel()))
            ->kebab()
            ->replace('-', ' ')
            ->plural()
            ->title();
    }

    public function getTable()
    {
        return Table::make($this->columns(), $this->filters())
            ->context(static::class)
            ->filterable($this->filterable)
            ->pagination($this->pagination)
            ->recordUrl(fn ($record) => $this->getResource()::generateUrl($this->recordRoute, ['record' => $record]))
            ->searchable($this->searchable)
            ->sortable($this->sortable);
    }

    protected function viewParameters()
    {
        return [
            'records' => $this->getRecords(),
        ];
    }
}
