<?php

namespace Filament\Tests\Fixtures\Resources\Posts;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * @extends resource<Post, PostResourceConfiguration>
 */
class ConfigurablePostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string | UnitEnum | null $navigationGroup = 'Blog';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $configurationClass = PostResourceConfiguration::class;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if ($configuration = static::getConfiguration()) {
            if ($configuration->isFeatured()) {
                $query->where('is_published', true);
            } elseif ($configuration->isArchived()) {
                $query->where('is_published', false);
            }
        }

        return $query;
    }

    public static function getNavigationLabel(): string
    {
        if ($configuration = static::getConfiguration()) {
            if ($label = $configuration->getNavigationLabel()) {
                return $label;
            }
        }

        return parent::getNavigationLabel();
    }

    public static function getNavigationGroup(): string | UnitEnum | null
    {
        if ($configuration = static::getConfiguration()) {
            if ($group = $configuration->getNavigationGroup()) {
                return $group;
            }
        }

        return parent::getNavigationGroup();
    }

    public static function getNavigationSort(): ?int
    {
        if ($configuration = static::getConfiguration()) {
            if ($sort = $configuration->getNavigationSort()) {
                return $sort;
            }
        }

        return parent::getNavigationSort();
    }

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                Forms\Components\TextInput::make('title')->required(),
                Forms\Components\MarkdownEditor::make('content'),
                Forms\Components\Select::make('author_id')
                    ->relationship('author', 'name')
                    ->required(),
                Forms\Components\TagsInput::make('tags'),
                Forms\Components\TextInput::make('rating')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->sortable()
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConfigurablePosts::route('/'),
            'create' => Pages\CreateConfigurablePost::route('/create'),
            'view' => Pages\ViewConfigurablePost::route('/{record}'),
            'edit' => Pages\EditConfigurablePost::route('/{record}/edit'),
        ];
    }
}
