<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasFieldWrapper
{
    protected string | Closure | null $fieldWrapperView = null;

    protected bool | Closure | null $hasInlineLabel = null;

    public function fieldWrapperView(string | Closure | null $view): static
    {
        $this->fieldWrapperView = $view;

        return $this;
    }

    public function inlineLabel(bool | Closure | null $condition = true): static
    {
        $this->hasInlineLabel = $condition;

        return $this;
    }

    public function getFieldWrapperView(): string
    {
        if ($this->hasInlineLabel()) {
            return 'forms::field-wrapper.inline';
        }

        return $this->getCustomFieldWrapperView() ??
            $this->getContainer()->getCustomFieldWrapperView() ??
            'forms::field-wrapper';
    }

    public function getCustomFieldWrapperView(): ?string
    {
        return $this->evaluate($this->fieldWrapperView);
    }

    public function hasInlineLabel(): ?bool
    {
        return $this->evaluate($this->hasInlineLabel) ?? $this->getContainer()->hasInlineLabel();
    }
}
