<?php

namespace Filament\Infolists;

use Filament\Support\Components\ViewComponent;
use Illuminate\Database\Eloquent\Model;

class ComponentContainer extends ViewComponent
{
    use Concerns\BelongsToParentComponent;
    use Concerns\CanBeHidden;
    use Concerns\Cloneable;
    use Concerns\HasColumns;
    use Concerns\HasComponents;
    use Concerns\HasEntryWrapper;
    use Concerns\HasInlineLabels;
    use Concerns\HasState;

    protected string $view = 'filament-infolists::component-container';

    protected string $evaluationIdentifier = 'container';

    protected string $viewIdentifier = 'container';

    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultEvaluationParameters(): array
    {
        return array_merge(parent::getDefaultEvaluationParameters(), [
            'record' => fn (): ?Model => $this->getRecord(),
        ]);
    }
}
