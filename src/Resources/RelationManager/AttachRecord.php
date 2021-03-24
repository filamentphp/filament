<?php

namespace Filament\Resources\RelationManager;

use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\HasForm;
use Illuminate\Support\Str;
use Livewire\Component;

class AttachRecord extends Component
{
    use Concerns\CanCallHooks;
    use HasForm;

    public $manager;

    public $owner;

    public $related;

    public function attach($another = false)
    {
        $manager = $this->manager;

        $this->validate();

        $this->callHook('beforeAttach');

        $this->owner->{$this->getRelationshipName()}()->attach($this->related);

        $this->callHook('afterAttach');

        $this->emit('refreshRelationManagerList', $manager);

        if (! $another) {
            $this->dispatchBrowserEvent('close', "{$manager}RelationManagerAttachModal");
        }

        $this->dispatchBrowserEvent('notify', __($manager::$attachModalAttachedMessage));

        $this->related = null;
    }

    public function getInverseRelationshipName()
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
        return $this->owner->{$this->getRelationshipName()}();
    }

    public function getRelationshipName()
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

    protected function form(Form $form)
    {
        return $form
            ->schema([
                Select::make('related')
                    ->label((string) Str::of($this->getRelationshipName())->singular()->ucfirst())
                    ->placeholder('filament::resources/relation-manager.modals.attach.form.related.placeholder')
                    ->getOptionSearchResultsUsing(function ($search) {
                        $relationship = $this->owner->{$this->getRelationshipName()}();

                        $query = $relationship->getRelated();

                        $search = Str::lower($search);
                        $searchOperator = [
                            'pgsql' => 'ilike',
                        ][$query->getConnection()->getDriverName()] ?? 'like';

                        return $query
                            ->where($this->getPrimaryColumn(), $searchOperator, "%{$search}%")
                            ->whereDoesntHave($this->getInverseRelationshipName(), function ($query) {
                                $query->where($this->owner->getQualifiedKeyName(), $this->owner->getKey());
                            })
                            ->pluck($this->getPrimaryColumn(), $query->getKeyName())
                            ->toArray();
                    })
                    ->required(),
            ]);
    }
}
