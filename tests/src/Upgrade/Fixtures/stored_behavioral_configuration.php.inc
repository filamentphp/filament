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

use Filament\Support\Enums\Alignment;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Builder;

function components(): array
{
    $builder = Builder::make('stored')->deletable(true)->label('original');
    $action = CreateAction::make()->tap(fn($configurationComponent) => $configurationComponent->evaluate(fn (): bool => true) ? $configurationComponent->modalAlignment(Alignment::Center) : $configurationComponent);
    $badge = Action::make('badge')->badge((string) 17);

    return [
        $builder->addable(fn (Builder $component) => !($component->getLabel() === 'original')),
        $action->createAnother(false),
        $badge->badge('Ready'),
    ];
}
