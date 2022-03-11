<?php

namespace Filament\Forms\Concerns;

use Closure;

trait HasFieldWrapper
{
    protected string | Closure | null $fieldWrapperView = null;

    public function fieldWrapperView(string | Closure | null $view): static
    {
        $this->fieldWrapperView = $view;

        return $this;
    }

    public function usesInlineWrapper(bool $condition = true): static
    {
        if ($condition) {
            $this->fieldWrapperView('forms::field-wrapper.inline');
            $this->columns(1);
        }

        return $this;
    }

    public function getFieldWrapperView(): string | null
    {
        return $this->evaluate($this->fieldWrapperView) ??
            $this->getParentComponent()?->getFieldWrapperView() ??
            'forms::field-wrapper';
    }
}
