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
];
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\LegacyColumnFactories;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\{BadgeColumn as Badge, BooleanColumn, TagsColumn};
use Filament\Tables\Columns\TextColumn;

$tags = TagsColumn::make('configured-later');
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
];
