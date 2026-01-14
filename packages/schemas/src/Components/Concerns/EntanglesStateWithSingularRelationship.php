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
use Illuminate\Support\Arr;
use Livewire\Component as LivewireComponent;

trait EntanglesStateWithSingularRelationship
{
    protected ?Model $cachedExistingRecord = null;

    protected ?string $relationship = null;

    protected string | Closure | null $relatedModel = null;

    protected ?Closure $mutateRelationshipDataBeforeCreateUsing = null;

    protected ?Closure $mutateRelationshipDataBeforeFillUsing = null;

    protected ?Closure $mutateRelationshipDataBeforeSaveUsing = null;

    protected bool | Closure $hasRelationship = false;

    public function relationship(string $name, bool | Closure $condition = true, string | Closure | null $relatedModel = null): static
    {
        $this->relationship = $name;
        $this->hasRelationship = $condition;
        $this->relatedModel = $relatedModel;
        $this->statePath($name);

        $this->loadStateFromRelationshipsUsing(static function (Component | CanEntangleWithSingularRelationships $component): void {
            $component->clearCachedExistingRecord();

            $findFirstComponentWithThisRelationship = function (Schema $schema) use ($component, &$findFirstComponentWithThisRelationship): ?CanEntangleWithSingularRelationships {
                foreach ($schema->getComponents(withActions: false, withHidden: true) as $childComponent) {
                    if (
                        ($childComponent->getModel() === $component->getModel()) &&
                        ($childComponent->getRecord() === $component->getRecord()) &&
                        ($childComponent instanceof CanEntangleWithSingularRelationships) &&
                        ($childComponent->getRelationshipName() === $component->getRelationshipName()) &&
                        ($childComponent->hasRelationship())
                    ) {
                        return $childComponent;
                    }

                    foreach ($childComponent->getChildSchemas() as $schema) {
                        $found = $findFirstComponentWithThisRelationship($schema);

                        if ($found) {
                            return $found;
                        }
                    }
                }

                return null;
            };

            $firstComponentWithThisRelationship = $findFirstComponentWithThisRelationship($component->getModelRootContainer());

            $isFirstComponent = ($firstComponentWithThisRelationship === null) || ($firstComponentWithThisRelationship === $component);

            if ($isFirstComponent) {
                // The first layout component using this relationship is the one that will fill the relationship data for all of them,
                // but it will only hydrate the state correctly for itself.
                $component->fillFromRelationship();
            } else {
                // If this is not the first layout component using this relationship, the data has already been filled by the first one,
                // so we just need to hydrate the state without calling any hydration hooks. This ensures that state casts have run.
                $hydratedDefaultState = null;
                $component->getChildSchema()->hydrateState($hydratedDefaultState, shouldCallHydrationHooks: false);
            }
        });

        $this->saveRelationshipsBeforeChildrenUsing(static function (Component | CanEntangleWithSingularRelationships $component, LivewireComponent & HasSchemas $livewire): void {
            // All layout components using this relationship should be saved together in this function.
            $componentsWithThisRelationship = [];

            $findComponentsWithThisRelationship = function (Schema $schema) use ($component, &$componentsWithThisRelationship, &$findComponentsWithThisRelationship): void {
                foreach ($schema->getComponents(withActions: false, withHidden: true) as $childComponent) {
                    if ($childComponent->isHidden() && (! $childComponent->shouldSaveRelationshipsWhenHidden())) {
                        continue;
                    }

                    if (
                        ($childComponent->getModel() === $component->getModel()) &&
                        ($childComponent->getRecord() === $component->getRecord()) &&
                        ($childComponent instanceof CanEntangleWithSingularRelationships) &&
                        ($childComponent->getRelationshipName() === $component->getRelationshipName()) &&
                        ($childComponent->hasRelationship())
                    ) {
                        $componentsWithThisRelationship[] = $childComponent;

                        continue;
                    }

                    foreach ($childComponent->getChildSchemas() as $schema) {
                        $findComponentsWithThisRelationship($schema);
                    }
                }
            };

            $findComponentsWithThisRelationship($component->getModelRootContainer());

            // The first layout component using this relationship is the one that will save the relationship for all of them.
            if (filled($componentsWithThisRelationship) && (Arr::first($componentsWithThisRelationship) !== $component)) {
                return;
            }

            $record = $component->getCachedExistingRecord();

            if (! $component->hasRelationship()) {
                $record?->delete();

                return;
            }

            $data = [];

            foreach ($componentsWithThisRelationship as $componentWithThisRelationship) {
                $data = [
                    ...$data,
                    ...$componentWithThisRelationship->getChildSchema()->getState(shouldCallHooksBefore: false),
                ];
            }

            $translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver();

            if ($record) {
                foreach ($componentsWithThisRelationship as $componentWithThisRelationship) {
                    $data = $componentWithThisRelationship->mutateRelationshipDataBeforeSave($data);
                }

                $translatableContentDriver ?
                    $translatableContentDriver->updateRecord($record, $data) :
                    $record->fill($data)->save();

                foreach ($componentsWithThisRelationship as $componentWithThisRelationship) {
                    $componentWithThisRelationship->cachedExistingRecord($record);
                }

                return;
            }

            $relationship = $component->getRelationship();
            $relatedModel = $component->getRelatedModel();

            foreach ($componentsWithThisRelationship as $componentWithThisRelationship) {
                $data = $componentWithThisRelationship->mutateRelationshipDataBeforeCreate($data);
            }

            if ($translatableContentDriver) {
                $record = $translatableContentDriver->makeRecord($relatedModel, $data);
            } else {
                $record = new $relatedModel;
                $record->fill($data);
            }

            if ($relationship instanceof BelongsTo) {
                $record->save();
                $relationship->associate($record);
                $relationship->getParent()->save();
            } else {
                $relationship->save($record);
            }

            foreach ($componentsWithThisRelationship as $componentWithThisRelationship) {
                $componentWithThisRelationship->cachedExistingRecord($record);
            }
        });

        $this->dehydrated(false);

        return $this;
    }

    public function fillFromRelationship(): void
    {
        $record = $this->getCachedExistingRecord();

        if (! $record) {
            $this->getChildSchema()->fill(shouldCallHydrationHooks: false, shouldFillStateWithNull: false);

            return;
        }

        $data = $this->mutateRelationshipDataBeforeFill(
            $this->getStateFromRelatedRecord($record),
        );

        $this->getChildSchema()->fill($data, shouldCallHydrationHooks: false, shouldFillStateWithNull: false);
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

    public function hasRelationship(): bool
    {
        return $this->evaluate($this->hasRelationship) && filled($this->getRelationshipName());
    }

    /**
     * @return class-string<Model>|null
     */
    public function getRelatedModel(): ?string
    {
        return $this->evaluate($this->relatedModel) ?? $this->getRelationship()?->getModel()::class;
    }

    public function cachedExistingRecord(?Model $record): static
    {
        $this->cachedExistingRecord = $record;

        $this->clearCachedDefaultChildSchemas();

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
