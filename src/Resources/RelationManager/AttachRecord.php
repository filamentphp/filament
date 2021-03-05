<?php

namespace Filament\Resources\RelationManager;

use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\HasForm;
use Illuminate\Support\Str;
use Livewire\Component;

class AttachRecord extends Component
{
    use HasForm;

    public $cancelButtonLabel;

    public $attachButtonLabel;

    public $addedMessage;

    public $manager;

    public $owner;

    public $related;

    public function submit()
    {
        $this->owner->{$this->getRelationship()}()->attach($this->related);

        $this->reset('related');

        $this->emit('refreshRelationManagerList', $this->manager);

        $this->dispatchBrowserEvent('close', "{$this->manager}RelationManagerAttachModal");
        $this->dispatchBrowserEvent('notify', $this->addedMessage);
    }

    public function getRelationship()
    {
        $manager = $this->manager;

        return $manager::$relationship;
    }

    public function getDisplayColumnName()
    {
        $manager = $this->manager;

        return $manager::$displayColumnName;
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
                    ->getDisplayValueUsing(fn ($value) => $value)
                    ->getOptionSearchResultsUsing(function ($search) {
                        $relationship = $this->owner->{$this->getRelationship()}();

                        $query = $relationship->getRelated();

                        $search = Str::lower($search);

                        return $query
                            ->whereRaw("LOWER({$this->getDisplayColumnName()}) LIKE ?", ["%{$search}%"])
                            ->pluck($this->getDisplayColumnName(), $query->getKeyName())
                            ->toArray();
                    }),
            ]);

        return $form;
    }

    public function render()
    {
        return view('filament::resources.relation-manager.attach-record');
    }
}
