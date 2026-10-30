<?php

namespace Filament\Tests\Upgrade\Fixtures\ApiParameters;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Imports\Events\ImportChunkProcessed;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\StateCasts\BooleanStateCast;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;

function configure(Get $get, Import $import, HasActions $livewire, ?Slider $slider): void
{
    Slider::make('price')->minValue(minValue: -4)->maxValue(maxValue: 90)->minDifference(minDifference: 3);
    $slider?->minValue(minValue: 2);
    Block::make('article')->maxItems(maxItems: 7);
    TextInput::make('title')->inlinePrefix(isInline: false)->inlineSuffix(...['isInline' => true]);
    RichEditor::make('body')->plugins(extensions: []);
    FileUpload::make('photo')->disk(name: 'public');
    NavigationItem::make('Products')->parentItem(group: 'Shop');
    Schema::make()->hidden(hidden: false);
    Actions::make([])->fullWidth(isFullWidth: false);
    TextEntry::make('title')->iconPosition(iconPosition: IconPosition::After);
    TextColumn::make('title')->iconPosition(iconPosition: IconPosition::Before);
    TextInputColumn::make('title')->inlinePrefix(isInline: false)->inlineSuffix(...['isInline' => true]);
    $get->string(key: 'title');
    $get->integer(key: 'count');
    $get->float(key: 'price');
    $get->boolean(key: 'active');
    $get->array(key: 'tags');
    $get->date(key: 'published_at');
    $get->enum(key: 'position', enumClass: IconPosition::class);
    $get->filled(key: 'title');
    $get->blank(key: 'title');
    Notification::make()->sendToDatabase(users: [], isEventDispatched: true);
    $livewire->getAction(name: ['edit']);
    $event = new ImportChunkProcessed(import: $import, columnMap: [], options: [], processedRows: 11, successfulRows: 7);
    $cast = new BooleanStateCast(isStoredAsInt: true);
}

class Banner extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'banner';
    }

    /**
     * @param array<string, mixed> $config
     * @param array<string, mixed> $data
     */
    public static function toHtml(array $config, array $data): ?string
    {
        $captured = static function () use ($config): array {
            return compact('config');
        };
        $shadowed = static fn (array $config): string => $config['title'];

        return $shadowed($captured()['config']) . $data['suffix'];
    }

    public static function toPreviewHtml(array $config): ?string
    {
        $config['title'] = strtoupper($config['title']);

        return static::toHtml(...[...func_get_args(), ['suffix' => '!']]);
    }
}

function render(): string
{
    return Banner::toPreviewHtml(config: ['title' => 'Title']);
}

class Unrelated
{
    public function minValue(int $minValue): int
    {
        return $minValue;
    }
}

function unchanged(Unrelated $other): void
{
    $other->minValue(minValue: 3);
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\ApiParameters;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Imports\Events\ImportChunkProcessed;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\StateCasts\BooleanStateCast;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;

function configure(Get $get, Import $import, HasActions $livewire, ?Slider $slider): void
{
    Slider::make('price')->minValue(value: -4)->maxValue(value: 90)->minDifference(difference: 3);
    $slider?->minValue(value: 2);
    Block::make('article')->maxItems(count: 7);
    TextInput::make('title')->inlinePrefix(condition: false)->inlineSuffix(...['condition' => true]);
    RichEditor::make('body')->plugins(plugins: []);
    FileUpload::make('photo')->disk(disk: 'public');
    NavigationItem::make('Products')->parentItem(parentItem: 'Shop');
    Schema::make()->hidden(condition: false);
    Actions::make([])->fullWidth(condition: false);
    TextEntry::make('title')->iconPosition(position: IconPosition::After);
    TextColumn::make('title')->iconPosition(position: IconPosition::Before);
    TextInputColumn::make('title')->inlinePrefix(condition: false)->inlineSuffix(...['condition' => true]);
    $get->string(path: 'title');
    $get->integer(path: 'count');
    $get->float(path: 'price');
    $get->boolean(path: 'active');
    $get->array(path: 'tags');
    $get->date(path: 'published_at');
    $get->enum(path: 'position', enumClass: IconPosition::class);
    $get->filled(path: 'title');
    $get->blank(path: 'title');
    Notification::make()->sendToDatabase(users: [], shouldDispatchEvent: true);
    $livewire->getAction(actions: ['edit']);
    $event = new ImportChunkProcessed(import: $import, columnMap: [], options: [], processedRowsCount: 11, successfulRowsCount: 7);
    $cast = new BooleanStateCast(isStoredAsInteger: true);
}

class Banner extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'banner';
    }

    /**
     * @param array<string, mixed> $configuration
     * @param array<string, mixed> $data
     */
    public static function toHtml(array $configuration, array $data): ?string
    {
        $config =& $configuration;
        $captured = static function () use ($config): array {
            return compact('config');
        };
        $shadowed = static fn (array $config): string => $config['title'];

        return $shadowed($captured()['config']) . $data['suffix'];
    }

    public static function toPreviewHtml(array $configuration): ?string
    {
        $config =& $configuration;
        $config['title'] = strtoupper($config['title']);

        return static::toHtml(...[...func_get_args(), ['suffix' => '!']]);
    }
}

function render(): string
{
    return Banner::toPreviewHtml(configuration: ['title' => 'Title']);
}

class Unrelated
{
    public function minValue(int $minValue): int
    {
        return $minValue;
    }
}

function unchanged(Unrelated $other): void
{
    $other->minValue(minValue: 3);
}
