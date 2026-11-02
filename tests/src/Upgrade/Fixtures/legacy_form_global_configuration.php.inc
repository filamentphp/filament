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
        \Filament\Tables\Columns\BadgeColumn::make('status'),
        \Filament\Tables\Columns\TagsColumn::make('tags')->limit(length: 2),
        \Filament\Tables\Columns\BooleanColumn::make('active'),
    ];
}

MultiSelect::configureUsing(fn (MultiSelect $component) => $component->multiple(false));
RelationshipRepeater::configureUsing(fn (RelationshipRepeater $component) => $component->relationship('custom'));
Placeholder::configureUsing(fn (Placeholder $component) => $component->content('Configured'));
Repeater::configureUsing(fn (Repeater $component) => $component->reorderable());
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\LegacyFormGlobalConfiguration;

use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\RelationshipRepeater;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;

function factories(): array
{
    return [
        Select::make('tags')->multiple(),
        Repeater::make('items')->relationship(),
        TextEntry::make('notice')->state('Notice'),
        TextColumn::make('status')->badge(),
        TextColumn::make('tags')->badge()->limitList(limit: 2),
        IconColumn::make('active')->boolean(),
    ];
}

MultiSelect::configureUsing(fn (MultiSelect $component) => $component->multiple(false));
RelationshipRepeater::configureUsing(fn (RelationshipRepeater $component) => $component->relationship('custom'));
Placeholder::configureUsing(fn (Placeholder $component) => $component->content('Configured'));
Repeater::configureUsing(fn (Repeater $component) => $component->reorderable());
