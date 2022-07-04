<?php

namespace Filament\Tables\Filters;

use Filament\Support\Components\Component;
use Illuminate\Support\Traits\Conditionable;

class BaseFilter extends Component
{
    use Concerns\BelongsToTable;
    use Concerns\CanBeHidden;
    use Concerns\CanSpanColumns;
    use Concerns\HasDefaultState;
    use Concerns\HasFormSchema;
    use Concerns\HasLabel;
    use Concerns\HasName;
    use Concerns\InteractsWithTableQuery;
    use Conditionable;

    protected string $evaluationIdentifier = 'filter';

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    protected function getDefaultEvaluationParameters(): array
    {
        return array_merge(parent::getDefaultEvaluationParameters(), [
            'livewire' => $this->getLivewire(),
        ]);
    }
}
