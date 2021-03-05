<?php

namespace Filament\Resources\RelationManager;

use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\HasForm;
use Illuminate\Support\Str;
use Livewire\Component;

class AddRecord extends Component
{
    use HasForm;

    public $cancelButtonLabel;

    public $addButtonLabel;

    public $addedMessage;

    public $manager;

    public $owner;

    public $related;

    public function add()
    {
        dd($this->related);
    }

    public function getRelationship()
    {
        $manager = $this->manager;

        return $manager::$relationship;
    }

    public function mount()
    {
        $this->fillWithFormDefaults();
    }

    public function getForm()
    {
        $form = Form::make()
            ->schema([
                Select::make('related')
                    ->placeholder('Start typing to search...')
                    ->getDisplayValueUsing(function ($value) {
                        if (! $value) return $value;
                    })
                    ->getOptionSearchResultsUsing(function ($search) {
                        $relationship = $this->owner->{$this->getRelationship()}();

                        $query = $relationship->getRelated();

                        $search = Str::lower('Est velit libero');

                        return $query
                            ->whereRaw("LOWER({$displayColumnName}) LIKE ?", ["%{$search}%"])
                            ->pluck($displayColumnName, $relationship->getOwnerKeyName())
                            ->toArray();
                    }),
            ]);

        return $form;
    }

    public function render()
    {
        return view('filament::resources.relation-manager.add-record');
    }
}
