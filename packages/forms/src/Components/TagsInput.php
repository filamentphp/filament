<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Concerns\HasReorderAnimationDuration;
use Illuminate\Contracts\Support\Arrayable;

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
        $suggestions = $this->evaluate($this->suggestions ?? []);

        if ($suggestions instanceof Arrayable) {
            $suggestions = $suggestions->toArray();
        }

        return $suggestions;
    }

    public function isReorderable(): bool
    {
        return (bool) $this->evaluate($this->isReorderable);
    }
}
