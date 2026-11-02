<?php

namespace Filament\Tests\Upgrade\Fixtures\StoredBehavioralConfiguration;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Builder;

function components(): array
{
    $builder = Builder::make('stored')->disableItemDeletion(false)->label('original');
    $action = CreateAction::make()->centerModal(condition: fn (): bool => true);
    $badge = Action::make('badge')->indicator(17);

    return [
        $builder->disableItemCreation(fn (Builder $component): bool => $component->getLabel() === 'original'),
        $action->disableCreateAnother(),
        $badge->indicator('Ready'),
    ];
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\StoredBehavioralConfiguration;

use Closure;
use Filament\Support\Enums\Alignment;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Builder;

function components(): array
{
    $builder = Builder::make('stored')->deletable(true)->label('original');
    $action = (static function (CreateAction $action, bool|Closure|null $condition): CreateAction {
        if ($action->evaluate($condition)) {
            $action->modalAlignment(Alignment::Center);
        }
        return $action;
    })(CreateAction::make(), fn (): bool => true);
    $badge = Action::make('badge')->badge((string) 17);

    return [
        (static fn(Builder $component, bool|Closure $condition): Builder => $component->addable(static fn(): bool => !$component->evaluate($condition)))($builder, fn (Builder $component): bool => $component->getLabel() === 'original'),
        $action->createAnother(false),
        $badge->badge('Ready'),
    ];
}
