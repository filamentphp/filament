<?php

namespace Filament\Tests\Fixtures\Resources\Posts;

use BackedEnum;
use Filament\Actions\Action;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use UnitEnum;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string | UnitEnum | null $navigationGroup = 'Blog';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $recordTitleAttribute = 'title';

    protected static int $globalSearchResultsLimit = 3;

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
            ->filters([
                Tables\Filters\Filter::make('is_published')
                    ->query(fn (Builder $query) => $query->where('is_published', true)),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('randomize_title')
                    ->databaseTransaction()
                    ->action(action: function (Post $record): void {
                        DB::afterCommit(function (): void {
                            throw new RuntimeException('This exception, happening after the successful commit of the current transaction, should not trigger a rollback by Filament.');
                        });

                        $record->title = Str::random(10);
                        $record->save();
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'view' => Pages\ViewPost::route('/{record}'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
