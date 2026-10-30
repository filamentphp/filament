<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedImagePresentationMethods;

use Closure;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;

function configure(ImageEntry $entry, ImageColumn $column, KeyValueEntry $keyValue, ?ImageEntry $nullableEntry, ?ImageColumn $nullableColumn, ?KeyValueEntry $nullableKeyValue, array $arguments): void
{
    $entry->height(height: null)->width(width: fn (): string => '7rem')->size(size: 63);
    $entry->getHeight();
    $entry->getWidth();
    $column->height(height: fn (): int => 37)->rounded(condition: false)->size(size: '5rem');
    $column->rounded();
    $column->getHeight();
    $column->isRounded();
    $keyValue->emptyMessage(message: fn (): string => 'No data');
    $keyValue->emptyMessage(null);
    $nullableEntry?->width(width: null)?->height(height: 51);
    $nullableColumn?->rounded(condition: fn (): bool => true);
    $nullableKeyValue?->emptyMessage(message: null);
    $callback = $entry->getWidth(...);
    $callback = $column->rounded(...);
    $callback = $keyValue->emptyMessage(...);
    $entry->HEIGHT(height: 41);
    $entry->size(...$arguments);
    $keyValue->emptyMessage(...$arguments);
    ImageEntry::make('avatar')->height(27)->width(49);
    ImageColumn::make('avatar')->rounded()->height(null);
    $entry->imageSize(64);
    $column->width(width: '10%')->getWidth();
    $method = 'height';
    $entry->{$method}(30);
    $entry->{'height'}(30);
    $callback = [$column, 'rounded'];
    ImageEntry::height(30);
}

class CustomEntry extends ImageEntry
{
    public function configureImage(): static
    {
        $this->height(35);

        return parent::width(47);
    }
}

class AliasOverridingEntry extends ImageEntry
{
    public function width(int | string | Closure | null $customWidth): static
    {
        return $this;
    }
}

class InheritedAliasOverridingEntry extends AliasOverridingEntry {}

class ReplacementOverridingColumn extends ImageColumn
{
    public function circular(bool | Closure $enabled = true): static
    {
        return $this;
    }
}

class InheritedReplacementOverridingColumn extends ReplacementOverridingColumn {}

class GetterOverridingEntry extends ImageEntry
{
    public function getImageWidth(): ?string
    {
        return '3rem';
    }
}

class CustomKeyValueEntry extends KeyValueEntry {}

class PlaceholderOverridingEntry extends KeyValueEntry
{
    public function placeholder(string | Htmlable | Closure | null $text): static
    {
        return $this;
    }
}

class InheritedPlaceholderOverridingEntry extends PlaceholderOverridingEntry {}

function subclasses(CustomEntry $custom, InheritedAliasOverridingEntry $alias, InheritedReplacementOverridingColumn $replacement, GetterOverridingEntry $getter, CustomKeyValueEntry $keyValue, InheritedPlaceholderOverridingEntry $placeholder, SpatieMediaLibraryImageEntry $mediaEntry, SpatieMediaLibraryImageColumn $mediaColumn, CustomEntry | AliasOverridingEntry $union): void
{
    $custom->height(35)->width(47)->getHeight();
    $alias->width(customWidth: 59);
    $alias?->imageHeight(35)?->width(customWidth: 59);
    $replacement->rounded(condition: false);
    $getter->getWidth();
    $keyValue->keyLabel('Property')->emptyMessage(message: 'Empty');
    $placeholder->keyLabel('Property')->emptyMessage(message: 'Empty');
    $mediaEntry->collection('avatars')->height(31)->width(53);
    $mediaColumn->collection('avatars')->rounded()->size(43);
    $union->width(59);
}

function unrelated(TextEntry $entry, TextColumn $column, UnrelatedComponent $unrelated, ImageEntry | UnrelatedComponent $union, $unknown): void
{
    $entry->size('lg');
    $column->size('lg')->width('10%')->getWidth();
    $unrelated->height(37);
    $unrelated->size(63);
    $unrelated->width(49);
    $unrelated->getHeight();
    $unrelated->getWidth();
    $unrelated->rounded();
    $unrelated->isRounded();
    $unrelated->emptyMessage(message: 'Empty');
    $unrelated->image()->height(37);
    $union->height(37);
    $unknown->size(63);
}

class UnrelatedComponent
{
    public function image(): ImageEntry
    {
        return ImageEntry::make('avatar');
    }
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedImagePresentationMethods;

use Closure;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;

function configure(ImageEntry $entry, ImageColumn $column, KeyValueEntry $keyValue, ?ImageEntry $nullableEntry, ?ImageColumn $nullableColumn, ?KeyValueEntry $nullableKeyValue, array $arguments): void
{
    $entry->imageHeight(height: null)->imageWidth(width: fn (): string => '7rem')->imageSize(size: 63);
    $entry->getImageHeight();
    $entry->getImageWidth();
    $column->imageHeight(height: fn (): int => 37)->circular(condition: false)->imageSize(size: '5rem');
    $column->circular();
    $column->getImageHeight();
    $column->isCircular();
    $keyValue->placeholder(placeholder: fn (): string => 'No data');
    $keyValue->placeholder(null);
    $nullableEntry?->imageWidth(width: null)?->imageHeight(height: 51);
    $nullableColumn?->circular(condition: fn (): bool => true);
    $nullableKeyValue?->placeholder(placeholder: null);
    $callback = $entry->getImageWidth(...);
    $callback = $column->circular(...);
    $callback = $keyValue->placeholder(...);
    $entry->imageHeight(height: 41);
    $entry->imageSize(...$arguments);
    $keyValue->emptyMessage(...$arguments);
    ImageEntry::make('avatar')->imageHeight(27)->imageWidth(49);
    ImageColumn::make('avatar')->circular()->imageHeight(null);
    $entry->imageSize(64);
    $column->width(width: '10%')->getWidth();
    $method = 'height';
    $entry->{$method}(30);
    $entry->{'height'}(30);
    $callback = [$column, 'rounded'];
    ImageEntry::height(30);
}

class CustomEntry extends ImageEntry
{
    public function configureImage(): static
    {
        $this->imageHeight(35);

        return parent::imageWidth(47);
    }
}

class AliasOverridingEntry extends ImageEntry
{
    public function width(int | string | Closure | null $customWidth): static
    {
        return $this;
    }
}

class InheritedAliasOverridingEntry extends AliasOverridingEntry {}

class ReplacementOverridingColumn extends ImageColumn
{
    public function circular(bool | Closure $enabled = true): static
    {
        return $this;
    }
}

class InheritedReplacementOverridingColumn extends ReplacementOverridingColumn {}

class GetterOverridingEntry extends ImageEntry
{
    public function getImageWidth(): ?string
    {
        return '3rem';
    }
}

class CustomKeyValueEntry extends KeyValueEntry {}

class PlaceholderOverridingEntry extends KeyValueEntry
{
    public function placeholder(string | Htmlable | Closure | null $text): static
    {
        return $this;
    }
}

class InheritedPlaceholderOverridingEntry extends PlaceholderOverridingEntry {}

function subclasses(CustomEntry $custom, InheritedAliasOverridingEntry $alias, InheritedReplacementOverridingColumn $replacement, GetterOverridingEntry $getter, CustomKeyValueEntry $keyValue, InheritedPlaceholderOverridingEntry $placeholder, SpatieMediaLibraryImageEntry $mediaEntry, SpatieMediaLibraryImageColumn $mediaColumn, CustomEntry | AliasOverridingEntry $union): void
{
    $custom->imageHeight(35)->imageWidth(47)->getImageHeight();
    $alias->width(customWidth: 59);
    $alias?->imageHeight(35)?->width(customWidth: 59);
    $replacement->rounded(condition: false);
    $getter->getWidth();
    $keyValue->keyLabel('Property')->placeholder(placeholder: 'Empty');
    $placeholder->keyLabel('Property')->emptyMessage(message: 'Empty');
    $mediaEntry->collection('avatars')->imageHeight(31)->imageWidth(53);
    $mediaColumn->collection('avatars')->circular()->imageSize(43);
    $union->width(59);
}

function unrelated(TextEntry $entry, TextColumn $column, UnrelatedComponent $unrelated, ImageEntry | UnrelatedComponent $union, $unknown): void
{
    $entry->size('lg');
    $column->size('lg')->width('10%')->getWidth();
    $unrelated->height(37);
    $unrelated->size(63);
    $unrelated->width(49);
    $unrelated->getHeight();
    $unrelated->getWidth();
    $unrelated->rounded();
    $unrelated->isRounded();
    $unrelated->emptyMessage(message: 'Empty');
    $unrelated->image()->imageHeight(37);
    $union->height(37);
    $unknown->size(63);
}

class UnrelatedComponent
{
    public function image(): ImageEntry
    {
        return ImageEntry::make('avatar');
    }
}
