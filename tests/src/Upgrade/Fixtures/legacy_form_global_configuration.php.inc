<?php

namespace Filament\Tests\Upgrade\Fixtures\LegacyFormGlobalConfiguration;

use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\RelationshipRepeater;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;

function factories(): array
{
    return [
        MultiSelect::make('tags'),
        RelationshipRepeater::make('items'),
        Placeholder::make('notice')->content('Notice'),
    ];
}

MultiSelect::configureUsing(fn (MultiSelect $component) => $component->multiple(false));
RelationshipRepeater::configureUsing(fn (RelationshipRepeater $component) => $component->relationship('custom'));
Placeholder::configureUsing(fn (Placeholder $component) => $component->content('Configured'));
Repeater::configureUsing(fn (Repeater $component) => $component->reorderable());
