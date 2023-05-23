<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait EntanglesStateWithSingularRelationship
{
    protected ?Model $cachedExistingRecord = null;

    protected string | null $relationship = null;

    protected ?Closure $mutateRelationshipDataBeforeCreateUsing = null;

    protected ?Closure $mutateRelationshipDataBeforeFillUsing = null;

    protected ?Closure $mutateRelationshipDataBeforeSaveUsing = null;

    public function relationship(string $relationshipName, bool | Closure $condition = true): static
    {
        $this->relationship = $relationshipName;
        $this->statePath($relationshipName);

        $this->loadStateFromRelationshipsUsing(static function (Component | CanEntangleWithSingularRelationships $component) {
            $component->clearCachedExistingRecord();

            $component->fillFromRelationship();
        });

        $this->saveRelationshipsBeforeChildrenUsing(static function (Component | CanEntangleWithSingularRelationships $component, HasForms $livewire) use ($condition): void {
            $record = $component->getCachedExistingRecord();

            if (! $component->evaluate($condition)) {
                $record?->delete();

                return;
            }

            if ($record) {
                return;
            }

            $relationship = $component->getRelationship();

            if ($relationship instanceof BelongsTo) {
                return;
            }

            $data = $component->getChildComponentContainer()->getState(shouldCallHooksBefore: false);
            $data = $component->mutateRelationshipDataBeforeCreate($data);

            $relatedModel = $component->getRelatedModel();

            $record = new $relatedModel();

            $activeLocale = $livewire->getActiveFormLocale();

            if ($activeLocale && method_exists($record, 'setLocale')) {
                $record->setLocale($activeLocale);
            }

            $record->fill($data);
            $relationship->save($record);

            $component->cachedExistingRecord($record);
        });

        $this->saveRelationshipsUsing(static function (Component | CanEntangleWithSingularRelationships $component, HasForms $livewire) use ($condition): void {
            if (! $component->evaluate($condition)) {
                return;
            }

            $data = $component->getChildComponentContainer()->getState(shouldCallHooksBefore: false);

            $record = $component->getCachedExistingRecord();

            $activeLocale = $livewire->getActiveFormLocale();

            if ($record) {
                $data = $component->mutateRelationshipDataBeforeSave($data);

                $activeLocale && method_exists($record, 'setLocale') && $record->setLocale($activeLocale);

                $record->fill($data)->save();

                return;
            }

            $relationship = $component->getRelationship();

            if (! ($relationship instanceof BelongsTo)) {
                return;
            }

            $data = $component->mutateRelationshipDataBeforeCreate($data);

            $relatedModel = $component->getRelatedModel();

            $record = new $relatedModel();

            if ($activeLocale && method_exists($record, 'setLocale')) {
                $record->setLocale($activeLocale);
            }

            $relationship->associate($record->create($data));
            $relationship->getParent()->save();

            $component->cachedExistingRecord($record);
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

        $data = $this->mutateRelationshipDataBeforeFill(
            $this->getStateFromRelatedRecord($record),
        );

        $this->getChildComponentContainer()->fill($data);
    }

    protected function getStateFromRelatedRecord(Model $record): array
    {
        $state = $record->attributesToArray();

        if (
            ($activeLocale = $this->getLivewire()->getActiveFormLocale()) &&
            method_exists($record, 'getTranslatableAttributes') &&
            method_exists($record, 'getTranslation')
        ) {
            foreach ($record->getTranslatableAttributes() as $attribute) {
                $state[$attribute] = $record->getTranslation($attribute, $activeLocale);
            }
        }

        return $state;
    }

    public function getChildComponentContainer($key = null): ComponentContainer
    {
        $container = parent::getChildComponentContainer($key);

        $relationship = $this->getRelationship();

        if (! $relationship) {
            return $container;
        }

        return $container->model($this->getCachedExistingRecord() ?? $this->getRelatedModel());
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

    public function cachedExistingRecord(?Model $record): static
    {
        $this->cachedExistingRecord = $record;

        return $this;
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

    public function mutateRelationshipDataBeforeCreateUsing(?Closure $callback): static
    {
        $this->mutateRelationshipDataBeforeCreateUsing = $callback;

        return $this;
    }

    public function mutateRelationshipDataBeforeCreate(array $data): array
    {
        if ($this->mutateRelationshipDataBeforeCreateUsing instanceof Closure) {
            $data = $this->evaluate($this->mutateRelationshipDataBeforeCreateUsing, [
                'data' => $data,
            ]);
        }

        return $data;
    }

    public function mutateRelationshipDataBeforeSaveUsing(?Closure $callback): static
    {
        $this->mutateRelationshipDataBeforeSaveUsing = $callback;

        return $this;
    }

    public function mutateRelationshipDataBeforeFill(array $data): array
    {
        if ($this->mutateRelationshipDataBeforeFillUsing instanceof Closure) {
            $data = $this->evaluate($this->mutateRelationshipDataBeforeFillUsing, [
                'data' => $data,
            ]);
        }

        return $data;
    }

    public function mutateRelationshipDataBeforeFillUsing(?Closure $callback): static
    {
        $this->mutateRelationshipDataBeforeFillUsing = $callback;

        return $this;
    }

    public function mutateRelationshipDataBeforeSave(array $data): array
    {
        if ($this->mutateRelationshipDataBeforeSaveUsing instanceof Closure) {
            $data = $this->evaluate($this->mutateRelationshipDataBeforeSaveUsing, [
                'data' => $data,
            ]);
        }

        return $data;
    }
}
