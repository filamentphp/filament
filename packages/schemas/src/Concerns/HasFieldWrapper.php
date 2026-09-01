<?php

namespace Filament\Schemas\Concerns;

use Closure;

trait HasFieldWrapper
{
    protected string | Closure | null $fieldWrapperView = null;

    public function fieldWrapperView(string | Closure | null $view): static
    {
        $this->fieldWrapperView = $view;

        return $this;
    }

    public function getCustomFieldWrapperView(): ?string
    {
        return $this->evaluate($this->fieldWrapperView) ??
            $this->getParentComponent()?->getCustomFieldWrapperView();
    }
}
