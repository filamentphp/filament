<?php

namespace Filament\Forms\Components;

class TagsInput extends Field
{
    use Concerns\HasPlaceholder;

    protected string $view = 'forms::components.tags-input';

    protected $separator = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (TagsInput $component, $state): void {
            if (is_array($state)) {
                return;
            }

            if ($separator = $component->getSeparator()) {
                $component->state(explode($separator, $state));

                return;
            }

            $component->state([]);
        });

        $this->dehydrateStateUsing(function (TagsInput $component, $state) {
            if ($separator = $component->getSeparator()) {
                return implode($separator, $state);
            }

            return $state;
        });

        $this->placeholder(__('forms::components.tags.placeholder'));
    }

    public function separator(string | callable $separator = ','): static
    {
        $this->separator = $separator;

        return $this;
    }

    public function getSeparator(): ?string
    {
        return $this->evaluate($this->separator);
    }
}
