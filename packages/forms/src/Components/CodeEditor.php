<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Support\Concerns\HasExtraAlpineAttributes;

class CodeEditor extends Field
{
    use HasExtraAlpineAttributes;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.code-editor';

    protected Language | Closure | null $language = null;

    protected bool | Closure | null $isWrapping = false;

    public function language(Language | Closure | null $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->evaluate($this->language);
    }

    public function isWrapping(): bool
    {
        return $this->evaluate($this->isWrapping);
    }

    public function wrapping(bool | Closure $condition = true): static
    {
        $this->isWrapping = $condition;

        return $this;
    }
}
