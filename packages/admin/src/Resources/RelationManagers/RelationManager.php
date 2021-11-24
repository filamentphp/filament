<?php

namespace Filament\Resources\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Livewire\Component;

class RelationManager extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public Model $ownerRecord;

    protected static string $relationship;

    protected static string $view;

    public static function getRelationshipName(): string
    {
        return static::$relationship;
    }

    protected function getRelationship(): Relation
    {
        return $this->ownerRecord->{static::getRelationshipName()}();
    }

    public function render(): View
    {
        return view(static::$view, $this->getViewData());
    }

    protected function getViewData(): array
    {
        return [];
    }
}
