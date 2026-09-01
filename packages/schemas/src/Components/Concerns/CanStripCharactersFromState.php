<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;
use Illuminate\Support\Arr;

trait CanStripCharactersFromState
{
    /**
     * @var string | array<string> | Closure | null
     */
    protected string | array | Closure | null $stripCharacters = null;

    /**
     * @var array<string>
     */
    protected array $cachedStripCharacters;

    /**
     * @param  string | array<string> | Closure | null  $characters
     */
    public function stripCharacters(string | array | Closure | null $characters): static
    {
        $this->stripCharacters = $characters;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getStripCharacters(): array
    {
        return $this->cachedStripCharacters ??= Arr::wrap($this->evaluate($this->stripCharacters));
    }

    public function hasStripCharacters(): bool
    {
        return filled($this->getStripCharacters());
    }

    protected function stripCharactersFromState(mixed $state): mixed
    {
        if (! is_string($state)) {
            return $state;
        }

        $stripCharacters = $this->getStripCharacters();

        if (empty($stripCharacters)) {
            return $state;
        }

        return str_replace($stripCharacters, '', $state);
    }
}
