<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Concerns\HasReorderAnimationDuration;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class TagsInput extends Field implements Contracts\HasAffixActions, Contracts\HasNestedRecursiveValidationRules
{
    use Concerns\HasAffixes;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasNestedRecursiveValidationRules;
    use Concerns\HasPlaceholder;
    use HasColor;
    use HasExtraAlpineAttributes;
    use HasReorderAnimationDuration;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.tags-input';

    protected bool | Closure $isReorderable = false;

    protected string | Closure | null $separator = null;

    /**
     * @var array<string> | Closure
     */
    protected array | Closure $splitKeys = [];

    /**
     * @var array<string> | Arrayable | Closure | null
     */
    protected array | Arrayable | Closure | null $suggestions = null;

    protected string | Closure | null $tagPrefix = null;

    protected string | Closure | null $tagSuffix = null;

    protected string | Closure | null $relationship = null;

    protected string | Closure $relationshipColumn = 'name';

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([]);

        $this->afterStateHydrated(static function (TagsInput $component, $state): void {
            if (is_array($state)) {
                return;
            }

            if (! ($separator = $component->getSeparator())) {
                $component->state([]);

                return;
            }

            $state = explode($separator, $state ?? '');

            if (count($state) === 1 && blank($state[0])) {
                $state = [];
            }

            $component->state($state);
        });

        $this->dehydrateStateUsing(static function (TagsInput $component, $state) {
            if ($separator = $component->getSeparator()) {
                return implode($separator, $state);
            }

            return $state;
        });

        $this->placeholder(__('filament-forms::components.tags_input.placeholder'));

        $this->reorderAnimationDuration(100);

        $this->configureRelationships();
    }

    protected function configureRelationships(): void
    {
        $this->loadStateFromRelationshipsUsing(static function (TagsInput $component, ?Model $record): void {
            if (! $component->checkRelationPresence($record, $component)) {
                return;
            }

            $relationship = $component->getRelationship();
            $relationshipColumn = $component->getRelationshipColumn();

            $record->loadMissing($relationship);

            $component->state(
                $record->{$relationship}()->select($relationshipColumn)->pluck($relationshipColumn)->all()
            );
        });

        $this->saveRelationshipsUsing(static function (TagsInput $component, ?Model $record, array $state) {
            if (! $component->checkRelationPresence($record, $component)) {
                return;
            }

            $relationship = $component->getRelationship();
            $relationshipColumn = $component->getRelationshipColumn();

            /** @var Model $related */
            $related = $record->{$relationship}()->getRelated();

            $tagIds = collect($state)
                ->map(fn ($tag) => $related->newQuery()->firstOrCreate([$relationshipColumn => $tag]))
                ->pluck($related->getKeyName())
                ->all();

            $record->{$relationship}()->sync($tagIds);
        });

        $this->dehydrated(fn (TagsInput $component) => $component->getRelationship() === null);
    }

    public function tagPrefix(string | Closure | null $prefix): static
    {
        $this->tagPrefix = $prefix;

        return $this;
    }

    public function tagSuffix(string | Closure | null $suffix): static
    {
        $this->tagSuffix = $suffix;

        return $this;
    }

    public function reorderable(bool | Closure $condition = true): static
    {
        $this->isReorderable = $condition;

        return $this;
    }

    public function separator(string | Closure | null $separator = ','): static
    {
        $this->separator = $separator;

        return $this;
    }

    public function relationship(string | Closure $column = 'name', string | Closure | null $relationship = null): static
    {
        $this->relationshipColumn = $column;

        $this->relationship = $relationship ?? Str::camel($this->name);

        return $this;
    }

    /**
     * @param  array<string> | Closure  $keys
     */
    public function splitKeys(array | Closure $keys): static
    {
        $this->splitKeys = $keys;

        return $this;
    }

    /**
     * @param  array<string> | Arrayable | Closure  $suggestions
     */
    public function suggestions(array | Arrayable | Closure $suggestions): static
    {
        $this->suggestions = $suggestions;

        return $this;
    }

    public function getTagPrefix(): ?string
    {
        return $this->evaluate($this->tagPrefix);
    }

    public function getTagSuffix(): ?string
    {
        return $this->evaluate($this->tagSuffix);
    }

    public function getSeparator(): ?string
    {
        return $this->evaluate($this->separator);
    }

    /**
     * @return array<string>
     */
    public function getSplitKeys(): array
    {
        return $this->evaluate($this->splitKeys) ?? [];
    }

    /**
     * @return array<string>
     */
    public function getSuggestions(): array
    {
        if ($this->checkRelationPresence($model = new ($this->getModel()), $this)) {
            return $model->{$this->getRelationship()}()
                ->getRelated()
                ->newQuery()
                ->select('name')
                ->orderBy('name')
                ->pluck('name')
                ->all();
        }

        $suggestions = $this->evaluate($this->suggestions ?? []);

        if ($suggestions instanceof Arrayable) {
            $suggestions = $suggestions->toArray();
        }

        return $suggestions;
    }

    public function getRelationship(): ?string
    {
        return $this->evaluate($this->relationship);
    }

    public function getRelationshipColumn(): string
    {
        return $this->evaluate($this->relationshipColumn);
    }

    public function isReorderable(): bool
    {
        return (bool) $this->evaluate($this->isReorderable);
    }

    protected function checkRelationPresence(Model $record, TagsInput $component): bool
    {
        $relationship = $component->getRelationship();

        // should we even be handling relationship?
        if ($relationship === null) {
            return false;
        }

        // make sure we have a relationship method
        if (! method_exists($record, $relationship)) {
            return false;
        }

        // and make sure it's a belongsToMany relationship
        if (! ($record->{$relationship}() instanceof BelongsToMany)) {
            return false;
        }

        return true;
    }
}
