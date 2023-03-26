<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\Concerns\HasColumns;
use Filament\Forms\Concerns\HasStateBindingModifiers;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasExtraAttributes;
use Illuminate\Database\Eloquent\Model;

class Component extends ViewComponent
{
    use Concerns\BelongsToContainer;
    use Concerns\BelongsToModel;
    use Concerns\CanBeConcealed;
    use Concerns\CanBeDisabled;
    use Concerns\CanBeHidden;
    use Concerns\CanSpanColumns;
    use Concerns\Cloneable;
    use Concerns\HasActions;
    use Concerns\HasChildComponents;
    use Concerns\HasFieldWrapper;
    use Concerns\HasInlineLabel;
    use Concerns\HasId;
    use Concerns\HasLabel;
    use Concerns\HasMaxWidth;
    use Concerns\HasMeta;
    use Concerns\HasState;
    use Concerns\ListensToEvents;
    use HasColumns;
    use HasExtraAttributes;
    use HasStateBindingModifiers;

    protected string $evaluationIdentifier = 'component';

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultEvaluationParameters(): array
    {
        $getOperationUsing = fn (): string => $this->getContainer()->getOperation();

        return array_merge(parent::getDefaultEvaluationParameters(), [
            'context' => $getOperationUsing,
            'get' => fn (): Get => $this->getGetCallback(),
            'livewire' => fn (): HasForms => $this->getLivewire(),
            'model' => fn (): ?string => $this->getModel(),
            'operation' => $getOperationUsing,
            'record' => fn (): ?Model => $this->getRecord(),
            'set' => fn (): Set => $this->getSetCallback(),
            'state' => fn (): mixed => $this->getState(),
        ]);
    }

    public function getKey(): ?string
    {
        return null;
    }
}
