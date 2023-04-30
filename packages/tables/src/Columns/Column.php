<?php

namespace Filament\Tables\Columns;

use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasExtraAttributes;
use Filament\Tables\Columns\Concerns\BelongsToLayout;
use Illuminate\Support\Traits\Conditionable;

class Column extends ViewComponent
{
    use BelongsToLayout;
    use Concerns\BelongsToTable;
    use Concerns\CanAggregateRelatedModels;
    use Concerns\CanBeDisabled;
    use Concerns\CanBeHidden;
    use Concerns\CanBeInline;
    use Concerns\CanBeSearchable;
    use Concerns\CanBeSortable;
    use Concerns\CanBeToggled;
    use Concerns\CanCallAction;
    use Concerns\CanGrow;
    use Concerns\CanOpenUrl;
    use Concerns\CanSpanColumns;
    use Concerns\HasAlignment;
    use Concerns\HasExtraCellAttributes;
    use Concerns\HasExtraHeaderAttributes;
    use Concerns\HasLabel;
    use Concerns\HasRowLoopObject;
    use Concerns\HasName;
    use Concerns\HasRecord;
    use Concerns\HasState;
    use Concerns\HasTooltip;
    use Concerns\InteractsWithTableQuery;
    use Conditionable;
    use HasExtraAttributes;

    protected string $evaluationIdentifier = 'column';

    protected string $viewIdentifier = 'column';

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
            'record' => $this->getRecord(),
            'rowLoop' => $this->getRowLoop(),
        ]);
    }
}
