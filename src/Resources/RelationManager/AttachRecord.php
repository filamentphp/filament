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

    public $attachAnotherButtonLabel;

    public $attachButtonLabel;

    public $attachedMessage;

    public $cancelButtonLabel;

    public $manager;

    public $owner;

    public $related;

    public function attach($another = false)
    {
        $this->validate();

        $this->owner->{$this->getRelationship()}()->attach($this->related);

        $this->emit('refreshRelationManagerList', $this->manager);

        if (! $another) {
            $this->dispatchBrowserEvent('close', "{$this->manager}RelationManagerAttachModal");
        }

        $this->dispatchBrowserEvent('notify', __($this->attachedMessage));

        $this->related = null;
    }

    protected function form(Form $form)
    {
        return $form
            ->schema([
                Select::make('related')
                    ->label((string) Str::of($this->getRelationship())->singular()->ucfirst())
                    ->placeholder('filament::resources/relation-manager.modals.attach.form.related.placeholder')
                    ->getOptionSearchResultsUsing(function ($search) {
                        $relationship = $this->owner->{$this->getRelationship()}();

                        $query = $relationship->getRelated();

                        $search = Str::lower($search);
                        $searchOperator = [
                            'pgsql' => 'ilike',
                        ][$query->getConnection()->getDriverName()] ?? 'like';

                        return $query
                            ->where($this->getPrimaryColumn(), $searchOperator, "%{$search}%")
                            ->whereDoesntHave($this->getInverseRelationship(), function ($query) {
                                $query->where($this->owner->getQualifiedKeyName(), $this->owner->getKey());
                            })
                            ->pluck($this->getPrimaryColumn(), $query->getKeyName())
                            ->toArray();
                    })
                    ->required(),
            ]);
    }

    public function getInverseRelationship()
    {
        $manager = $this->manager;

        if (property_exists($manager, 'inverseRelationship')) {
            return $manager::$inverseRelationship;
        }

        return (string) Str::of(class_basename($this->owner))
            ->lower()
            ->plural()
            ->camel();
    }

    public function getPrimaryColumn()
    {
        return $this->manager::getPrimaryColumn() ?? $this->owner->getKeyName();
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

    public function render()
    {
        return view('filament::resources.relation-manager.attach-record');
    }
}
