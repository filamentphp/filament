<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Support\Concerns\CanWrap;
use Filament\Support\Concerns\HasExtraAlpineAttributes;

class CodeEditor extends Field
{
    use CanWrap;
    use HasExtraAlpineAttributes;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.code-editor';

    protected Language | Closure | null $language = null;

    public function language(Language | Closure | null $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->evaluate($this->language);
    }
}
