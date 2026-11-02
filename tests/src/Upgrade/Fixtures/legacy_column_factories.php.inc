<?php

namespace Filament\Tests\Upgrade\Fixtures\LegacyColumnFactories;

use Filament\Tables\Columns\{BadgeColumn as Badge, BooleanColumn, TagsColumn};
use Filament\Tables\Columns\TextColumn;

$tags = TagsColumn::make('configured-later');
$tags->limit(2);

return [
    Badge::make(name: name())->label(label())->color('success')->limit(length: 9, end: '!'),
    \Filament\Tables\Columns\BadgeColumn::make('status'),
    BooleanColumn::make(name: 'active')->label('Active'),
    TagsColumn::make('tags'),
    TagsColumn::make('tags')->limit(),
    TagsColumn::make('tags')->limit(length: null),
    TagsColumn::make('tags')->limit(length: fn () => 2)->separator(','),
    TagsColumn::make('tags')->limit(nextLimit())->limitList(4)->limit(1)->badge(false),
    TextColumn::make('title')->limit(length: 12, end: '...'),
    Badge::make('badge')->badge(false)->label('Forced'),
    Badge::make('badge')->badge(condition: sideEffect()),
    BooleanColumn::make('active')->boolean(false),
    BooleanColumn::make('active')->boolean(fn () => sideEffect()),
    TagsColumn::make('tags')->tooltip('Tags')->toggleable()->alignCenter(),
    TagsColumn::make('tags')->tap(fn ($column) => $column->label('Tags'))->limit(2),
    TagsColumn::make('tags')->when($condition, fn ($column) => $column->color('success')),
    Badge::make('badge')->unless($condition, fn ($column) => $column->sortable())->searchable(),
];
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\LegacyColumnFactories;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\{BadgeColumn as Badge, BooleanColumn, TagsColumn};
use Filament\Tables\Columns\TextColumn;

$tags = TextColumn::make('configured-later')->badge();
$tags->limit(2);

return [
    TextColumn::make(name: name())->badge()->label(label())->color('success')->limit(length: 9, end: '!'),
    TextColumn::make('status')->badge(),
    IconColumn::make(name: 'active')->boolean()->label('Active'),
    TextColumn::make('tags')->badge(),
    TextColumn::make('tags')->badge()->limitList(),
    TextColumn::make('tags')->badge()->limitList(limit: null),
    TextColumn::make('tags')->badge()->limitList(limit: fn () => 2)->separator(','),
    TextColumn::make('tags')->badge()->limitList(nextLimit())->limitList(4)->limitList(1)->badge(false),
    TextColumn::make('title')->limit(length: 12, end: '...'),
    TextColumn::make('badge')->badge()->badge(false)->badge()->label('Forced'),
    TextColumn::make('badge')->badge()->badge(condition: sideEffect())->badge(),
    IconColumn::make('active')->boolean()->boolean(false)->boolean(),
    IconColumn::make('active')->boolean()->boolean(fn () => sideEffect())->boolean(),
    TextColumn::make('tags')->badge()->tooltip('Tags')->toggleable()->alignCenter(),
    TextColumn::make('tags')->badge()->tap(fn ($column) => $column->label('Tags'))->limitList(2),
    TextColumn::make('tags')->badge()->when($condition, fn ($column) => $column->color('success')),
    TextColumn::make('badge')->badge()->unless($condition, fn ($column) => $column->sortable())->searchable(),
];
