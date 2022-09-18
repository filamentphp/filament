<?php

namespace Filament\Tables\Columns\Layout;

use Closure;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasExtraAttributes;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Concerns\BelongsToLayout;
use Filament\Tables\Columns\Concerns\BelongsToTable;
use Filament\Tables\Columns\Concerns\CanBeHidden;
use Filament\Tables\Columns\Concerns\CanGrow;
use Filament\Tables\Columns\Concerns\HasRecord;
use Illuminate\Support\Traits\Conditionable;

class Component extends ViewComponent
{
    use BelongsToLayout;
    use BelongsToTable;
    use CanBeHidden;
    use CanGrow;
    use HasRecord;
    use Conditionable;
    use HasExtraAttributes;

    protected string $evaluationIdentifier = 'layout';

    protected string $viewIdentifier = 'layout';

    protected array | Closure $components = [];

    public function schema(array | Closure $schema): static
    {
        $this->components($schema);

        return $this;
    }

    public function components(array | Closure $components): static
    {
        $this->components = $components;

        return $this;
    }

    public function getColumns(): array
    {
        $columns = [];

        foreach ($this->getComponents() as $component) {
            if ($component instanceof Column) {
                $columns[$component->getName()] = $component;

                continue;
            }

            $columns = array_merge($columns, $component->getColumns());
        }

        return $columns;
    }

    public function getComponents(): array
    {
        return array_map(function (Component | Column $component): Component | Column {
            return $component->layout($this);
        }, $this->evaluate($this->components));
    }

    protected function getDefaultEvaluationParameters(): array
    {
        return array_merge(parent::getDefaultEvaluationParameters(), [
            'livewire' => $this->getLivewire(),
            'record' => $this->getRecord(),
        ]);
    }
}
