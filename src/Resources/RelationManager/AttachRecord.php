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

    public $owner;

    public $manager;

    public $related;

    public function attach($another = false)
    {
        $manager = $this->getManager();

        $this->validate();

        $this->getOwner()->{$this->getRelationshipName()}()->attach($this->getRelated());

        $this->emit('refreshRelationManagerList', $manager);

        if (! $another) {
            $this->dispatchBrowserEvent('close', "{$manager}RelationManagerAttachModal");
        }

        $this->dispatchBrowserEvent('notify', __($manager::$attachModalAttachedMessage));

        $this->related = null;
    }

    protected function form(Form $form)
    {
        return $form
            ->schema([
                Select::make('related')
                    ->label((string) Str::of($this->getRelationshipName())->singular()->ucfirst())
                    ->placeholder('filament::resources/relation-manager.modals.attach.form.related.placeholder')
                    ->getOptionSearchResultsUsing(function ($search) {
                        $relationship = $this->getOwner()->{$this->getRelationshipName()}();

                        $query = $relationship->getRelated();

                        $search = Str::lower($search);
                        $searchOperator = [
                            'pgsql' => 'ilike',
                        ][$query->getConnection()->getDriverName()] ?? 'like';

                        return $query
                            ->where($this->getPrimaryColumn(), $searchOperator, "%{$search}%")
                            ->whereDoesntHave($this->getInverseRelationshipName(), function ($query) {
                                $query->where($this->getOwner()->getQualifiedKeyName(), $this->getOwner()->getKey());
                            })
                            ->pluck($this->getPrimaryColumn(), $query->getKeyName())
                            ->toArray();
                    })
                    ->required(),
            ]);
    }

    public function getInverseRelationshipName()
    {
        $manager = $this->getManager();

        if (property_exists($manager, 'inverseRelationship')) {
            return $manager::$inverseRelationship;
        }

        return (string) Str::of(class_basename($this->getOwner()))
            ->lower()
            ->plural()
            ->camel();
    }

    public function getPrimaryColumn()
    {
        return $this->getManager()::getPrimaryColumn() ?? $this->getOwner()->getKeyName();
    }

    public function getRelationship()
    {
        return $this->getOwner()->{$this->getRelationshipName()}();
    }

    public function getRelationshipName()
    {
        $manager = $this->getManager();

        return $manager::$relationship;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function getManager()
    {
        return $this->manager;
    }

    public function getRelated()
    {
        return $this->related;
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
