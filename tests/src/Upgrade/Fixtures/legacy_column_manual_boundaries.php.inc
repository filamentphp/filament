<?php

namespace Filament\Tests\Upgrade\Fixtures\LegacyColumnManualBoundaries;

use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;

trait CustomLimit
{
    public function limit($length = 8, $end = null): static
    {
        return parent::limit($length, $end);
    }
}

class CustomTags extends TagsColumn
{
    use CustomLimit;
}

class IntermediateTags extends CustomTags {}

class CustomBadge extends BadgeColumn
{
    public function isBadge(): bool
    {
        return parent::isBadge();
    }
}

class CustomBoolean extends BooleanColumn
{
    public function isBoolean($state = null, $relatedRecord = null): bool
    {
        return parent::isBoolean($state, $relatedRecord);
    }
}

/** @param array<TagsColumn> $tags */
function configure(BadgeColumn|TextColumn $badge, BooleanColumn $boolean, array $tags): BadgeColumn
{
    $badge->badge(false);
    $boolean->boolean(false);
    $tags[0]->limit(2, end: sideEffect());

    return $badge;
}

TagsColumn::configureUsing(fn (TagsColumn $column) => $column->limit(2));
BadgeColumn::configureUsing(fn (BadgeColumn $column) => $column->badge(false));
BooleanColumn::configureUsing(fn (BooleanColumn $column) => $column->boolean(false));

return [
    BadgeColumn::make('badge')->color(fn (BadgeColumn $column) => 'success'),
    BooleanColumn::make('active')->icon(fn (BooleanColumn $column) => 'check'),
    TagsColumn::make('tags')->limit(fn (TagsColumn $column) => 2),
    TagsColumn::make('tags')->limit(2, sideEffect())->label('Tags'),
    TagsColumn::make('tags')->limit(end: sideEffect()),
    TagsColumn::make('tags')->limit(2, end: null),
    TagsColumn::make('tags')->limit(end: fn () => sideEffect(), length: 2),
    TagsColumn::make('tags')->limit(...$arguments),
    TagsColumn::make('tags')->limit(...),
    TagsColumn::make('tags')->tap($configure)->limit(2),
    TagsColumn::make('tags')->when($condition, $configure),
    TagsColumn::make('tags')->customMethod(),
    TagsColumn::make('tags')->{$method}(2),
    TagsColumn::make('tags')?->limit(2),
    BadgeColumn::make('badge')->badge(false)->label('Forced'),
    BadgeColumn::make('badge')->badge(condition: sideEffect()),
    BooleanColumn::make('active')->boolean(false),
    BooleanColumn::make('active')->boolean(fn () => sideEffect()),
    CustomTags::make('tags')->limit(2),
    IntermediateTags::make('tags')->limit(2),
    CustomBadge::make('badge'),
    CustomBoolean::make('active'),
    TagsColumn::make(...),
    new TagsColumn('tags'),
    TagsColumn::class,
    $column instanceof BadgeColumn,
    'Filament\\Tables\\Columns\\BooleanColumn',
    $class::make('tags'),
    TextColumn::make('title')->limit(2),
];
