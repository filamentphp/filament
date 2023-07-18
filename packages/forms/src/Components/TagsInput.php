<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Illuminate\Contracts\Support\Arrayable;

class TagsInput extends Field implements Contracts\HasNestedRecursiveValidationRules
{
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasNestedRecursiveValidationRules;
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.tags-input';

    protected string | Closure | null $separator = null;

    /**
     * @var array<string> | Closure
     */
    protected array | Closure $splitKeys = [];

    /**
     * @var array<string> | Arrayable | Closure | null
     */
    protected array | Arrayable | Closure | null $suggestions = null;

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
}
