<?php

namespace Filament\Forms\Concerns;

use Closure;

trait HasFieldWrapper
{
    protected string | Closure | null $fieldWrapperView = null;

    protected bool | Closure | null $hasInlineLabels = null;

    public function fieldWrapperView(string | Closure | null $view): static
    {
        $this->fieldWrapperView = $view;

        return $this;
    }

    public function inlineLabel(bool | Closure | null $condition = true): static
    {
        $this->hasInlineLabels = $condition;

        return $this;
    }

    public function getCustomFieldWrapperView(): ?string
    {
        return $this->evaluate($this->fieldWrapperView) ??
            $this->getParentComponent()?->getCustomFieldWrapperView();
    }

    public function hasInlineLabel(): ?bool
    {
        return $this->evaluate($this->hasInlineLabels) ?? $this->getParentComponent()?->hasInlineLabel();
    }
}
