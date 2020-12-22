<?php

namespace Filament\Fields;

use Filament\Fields\Checkbox;

class Choices extends BaseField {
    public $type = 'checkbox';
    public $options = [];

    /**
     * @return static
     */
    public function options(array $options): self
    {
        foreach ($options as $value => $label) {
            $this->options[] = Checkbox::make($this->model)
                                    ->type($this->type)
                                    ->label($label)
                                    ->hideErrorOutput()
                                    ->extraAttributes([
                                        'name' => $this->name(),
                                    ])
                                    ->value($value);
        }
        return $this;
    }

    /**
     * @return static
     */
    public function radio(): self
    {
        $this->type = 'radio';
        return $this;
    }

    /**
     * @return string
     */
    protected function name(): string
    {
        return $this->type === 'radio' ? $this->model : $this->model.'[]';
    }
}