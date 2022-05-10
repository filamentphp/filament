<?php

namespace Filament\Forms\Components\Concerns;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Translatable\HasTranslations;

trait EntanglesStateWithSingularRelationship
{
    protected ?Model $cachedExistingRecord = null;

    protected string | null $relationship = null;

    public function relationship(string $relationshipName): static
    {
        $this->relationship = $relationshipName;
        $this->statePath($relationshipName);

        $this->loadStateFromRelationshipsUsing(static function (Component $component) {
            /** @var Component|CanEntangleWithSingularRelationships $component */

            $component->clearCachedExistingRecord();

            $component->fillFromRelationship();
        });

        $this->saveRelationshipsUsing(static function (Component $component, HasForms $livewire, $state): void {
            /** @var Component|CanEntangleWithSingularRelationships $component */

            $record = $component->getCachedExistingRecord();

            $activeLocale = $livewire->getActiveFormLocale();

            if ($record) {
                if ($activeLocale && method_exists($record, 'setLocale')) {
                    $record->setLocale($activeLocale);
                }

                $record->fill($state)->save();

                return;
            }

            $relatedModel = $component->getRelatedModel();
            $record = $activeLocale && method_exists($relatedModel, 'usingLocale') ?
                $relatedModel::usingLocale($activeLocale) :
                new $relatedModel();
            $record->fill($state);

            $relationship = $component->getRelationship();
            match ($relationship::class) {
                BelongsTo::class => $relationship->associate($record->save()),
                HasOne::class => $relationship->save($record),
                MorphOne::class => $relationship->save($record),
            };
        });

        $this->dehydrated(false);

        return $this;
    }

    public function fillFromRelationship(): void
    {
        $record = $this->getCachedExistingRecord();

        if (! $record) {
            $this->getChildComponentContainer()->fill();

            return;
        }

        $data = $record->toArray();

        $activeLocale = $this->getLivewire()->getActiveFormLocale();

        if ($activeLocale && method_exists($record, 'getTranslatableAttributes') && method_exists($record, 'getTranslation')) {
            foreach ($record->getTranslatableAttributes() as $attribute) {
                $data[$attribute] = $record->getTranslation($attribute, $activeLocale);
            }
        }

        $this->getChildComponentContainer()->fill($data);
    }

    public function getChildComponentContainer(): ComponentContainer
    {
        $relationship = $this->getRelationship();

        if (! $relationship) {
            return parent::getChildComponentContainer();
        }

        return parent::getChildComponentContainer()
            ->model($this->getCachedExistingRecord() ?? $this->getRelatedModel());
    }

    public function getRelationship(): BelongsTo | HasOne | MorphOne | null
    {
        $name = $this->getRelationshipName();

        if (blank($name)) {
            return null;
        }

        return $this->getModelInstance()->{$name}();
    }

    public function getRelationshipName(): ?string
    {
        return $this->relationship;
    }

    public function getRelatedModel(): ?string
    {
        return $this->getRelationship()?->getModel()::class;
    }

    public function getCachedExistingRecord(): ?Model
    {
        if ($this->cachedExistingRecord) {
            return $this->cachedExistingRecord;
        }

        $record = $this->getRelationship()?->getResults();

        if (! $record?->exists) {
            return null;
        }

        return $this->cachedExistingRecord = $record;
    }

    public function clearCachedExistingRecord(): void
    {
        $this->cachedExistingRecord = null;
    }
}
