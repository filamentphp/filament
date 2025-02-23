<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Livewire\Component as LivewireComponent;

trait EntanglesStateWithSingularRelationship
{
    protected ?Model $cachedExistingRecord = null;

    protected ?string $relationship = null;

    protected ?Closure $mutateRelationshipDataBeforeCreateUsing = null;

    protected ?Closure $mutateRelationshipDataBeforeFillUsing = null;

    protected ?Closure $mutateRelationshipDataBeforeSaveUsing = null;

    public function relationship(string $name, bool | Closure $condition = true): static
    {
        $this->relationship = $name;
        $this->statePath($name);

        $this->loadStateFromRelationshipsUsing(static function (Component | CanEntangleWithSingularRelationships $component): void {
            $component->clearCachedExistingRecord();

            $component->fillFromRelationship();
        });

        $this->saveRelationshipsBeforeChildrenUsing(static function (Component | CanEntangleWithSingularRelationships $component, LivewireComponent & HasSchemas $livewire) use ($condition): void {
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

            $data = $component->getChildSchema()->getState(shouldCallHooksBefore: false);
            $data = $component->mutateRelationshipDataBeforeCreate($data);

            $relatedModel = $component->getRelatedModel();

            $translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver();

            if ($translatableContentDriver) {
                $record = $translatableContentDriver->makeRecord($relatedModel, $data);
            } else {
                $record = new $relatedModel;
                $record->fill($data);
            }

            $relationship->save($record);

            $component->cachedExistingRecord($record);
        });

        $this->saveRelationshipsUsing(static function (Component | CanEntangleWithSingularRelationships $component, LivewireComponent & HasSchemas $livewire) use ($condition): void {
            if (! $component->evaluate($condition)) {
                return;
            }

            $data = $component->getChildSchema()->getState(shouldCallHooksBefore: false);

            $record = $component->getCachedExistingRecord();

            $translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver();

            if ($record) {
                $data = $component->mutateRelationshipDataBeforeSave($data);

                $translatableContentDriver ?
                    $translatableContentDriver->updateRecord($record, $data) :
                    $record->fill($data)->save();

                return;
            }

            $relationship = $component->getRelationship();

            if (! ($relationship instanceof BelongsTo)) {
                return;
            }

            $data = $component->mutateRelationshipDataBeforeCreate($data);

            $relatedModel = $component->getRelatedModel();

            if ($translatableContentDriver) {
                $record = $translatableContentDriver->makeRecord($relatedModel, $data);
            } else {
                $record = new $relatedModel;
                $record->fill($data);
            }

            $record->save();

            $relationship->associate($record);
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
            $this->getChildSchema()->fill(andCallHydrationHooks: false, andFillStateWithNull: false);

            return;
        }

        $data = $this->mutateRelationshipDataBeforeFill(
            $this->getStateFromRelatedRecord($record),
        );

        $this->getChildSchema()->fill($data, andCallHydrationHooks: false, andFillStateWithNull: false);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getStateFromRelatedRecord(Model $record): array
    {
        if ($translatableContentDriver = $this->getLivewire()->makeFilamentTranslatableContentDriver()) {
            return $translatableContentDriver->getRecordAttributesToArray($record);
        }

        return $record->attributesToArray();
    }

    /**
     * @param  array-key  $key
     */
    public function getChildSchema($key = null): ?Schema
    {
        $container = parent::getChildSchema($key);

        if (! $container) {
            return null;
        }

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

    /**
     * @return class-string<Model>|null
     */
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

        $parentRecord = $this->getRecord();

        if (! $parentRecord) {
            return null;
        }

        $relationshipName = $this->getRelationshipName();

        if (blank($relationshipName)) {
            return null;
        }

        if ($parentRecord->relationLoaded($relationshipName)) {
            $record = $parentRecord->getRelationValue($relationshipName);
        } else {
            $record = $this->getRelationship()?->getResults();
        }

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

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
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

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
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

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
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
