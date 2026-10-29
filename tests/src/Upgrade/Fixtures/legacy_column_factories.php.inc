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

use Filament\Tables\Columns\{BadgeColumn as Badge, BooleanColumn, TagsColumn};
use Filament\Tables\Columns\TextColumn;

$tags = TagsColumn::make('configured-later');
$tags->limit(2);

return [
    Badge::make(name: name())->label(label())->color('success')->limit(length: 9, end: '!'),
    Badge::make('status'),
    BooleanColumn::make(name: 'active')->label('Active'),
    TagsColumn::make('tags'),
    TagsColumn::make('tags')->limit(),
    TagsColumn::make('tags')->limit(length: null),
    TagsColumn::make('tags')->limit(length: fn () => 2)->separator(','),
    TagsColumn::make('tags')->limit(nextLimit())->limitList(4)->limit(1)->badge(false),
    TextColumn::make('title')->limit(length: 12, end: '...'),
];
