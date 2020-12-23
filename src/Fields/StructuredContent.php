<?php

namespace Filament\Fields;

class StructuredContent extends BaseField {
    public $blocks = [];
    public $buttonLabel = 'Add Block';

    /**
     * @return static
     */
    public function block(string $model, string $label, array $fields): self
    {
        // $this->blocks[] = $block;
        return $this;
    }

    /**
     * @return static
     */
    public function buttonLabel(string $buttonLabel): self
    {
        $this->buttonLabel = $buttonLabel;
        return $this;
    }
}