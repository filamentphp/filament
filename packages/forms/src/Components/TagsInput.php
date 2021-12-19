<?php

namespace Filament\Forms\Components;

class TagsInput extends Field
{
    use Concerns\HasExtraAlpineAttributes;
    use Concerns\HasPlaceholder;

    protected string $view = 'forms::components.tags-input';

    protected $separator = null;

    protected $suggestions = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (TagsInput $component, $state): void {
            if (is_array($state)) {
                return;
            }

            if (! ($separator = $component->getSeparator())) {
                $component->state([]);

                return;
            }

            $state = explode($separator, $state);

            if (count($state) === 1 && blank($state[0])) {
                $state = [];
            }

            $component->state($state);
        });

        $this->dehydrateStateUsing(function (TagsInput $component, $state) {
            if ($separator = $component->getSeparator()) {
                return implode($separator, $state);
            }

            return $state;
        });

        $this->placeholder(__('forms::components.tags_input.placeholder'));
    }

    public function separator(string | callable $separator = ','): static
    {
        $this->separator = $separator;

        return $this;
    }

    public function suggestions(array | callable $suggestions): static
    {
        $this->suggestions = $suggestions;

        return $this;
    }

    public function getSeparator(): ?string
    {
        return $this->evaluate($this->separator);
    }

    public function getSuggestions(): array
    {
        return $this->evaluate($this->suggestions ?? []);
    }
}
