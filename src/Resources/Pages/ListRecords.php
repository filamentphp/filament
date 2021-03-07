<?php

namespace Filament\Resources\Pages;

use Filament\Filament;
use Filament\Resources\Tables\Table;
use Filament\Tables\HasTable;
use Illuminate\Support\Str;

class ListRecords extends Page
{
    use HasTable;

    public static $createButtonLabel = 'filament::resources/pages/list-records.buttons.create.label';

    public static $view = 'filament::resources.pages.list-records';

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
        return static::getResource()::table(Table::make())
            ->context(static::class)
            ->filterable($this->filterable)
            ->pagination($this->pagination)
            ->recordUrl(fn ($record) => $this->getResource()::generateUrl($this->recordRoute, ['record' => $record]))
            ->searchable($this->searchable)
            ->sortable($this->sortable);
    }

    public function canCreate()
    {
        return Filament::can('create', static::getModel());
    }

    public function isAuthorized()
    {
        return Filament::can('viewAny', static::getModel());
    }

    public function viewParameters()
    {
        return [
            'records' => $this->getRecords(),
        ];
    }
}
