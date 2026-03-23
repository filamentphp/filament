<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use BackedEnum;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Infolists;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'slug', 'author.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Author' => $record->author?->name,
            'Status' => ucfirst($record->status),
        ];
    }

    public static function getGlobalSearchResultActions(Model $record): array
    {
        return [
            Action::make('edit')
                ->url(static::getUrl('edit', ['record' => $record]))
                ->icon('heroicon-m-pencil-square'),
            Action::make('view')
                ->url(static::getUrl('view', ['record' => $record])),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with('author');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('author_id')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'reviewing' => 'Reviewing',
                        'published' => 'Published',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('rating')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(10),
                Forms\Components\Toggle::make('is_featured'),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Infolists\Components\TextEntry::make('title')
                    ->weight(FontWeight::Bold)
                    ->columnSpanFull(),
                Infolists\Components\TextEntry::make('slug'),
                Infolists\Components\TextEntry::make('author.name'),
                Infolists\Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'reviewing' => 'warning',
                        'published' => 'success',
                    }),
                Infolists\Components\TextEntry::make('rating'),
                Infolists\Components\IconEntry::make('is_featured')
                    ->boolean(),
                Infolists\Components\TextEntry::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'reviewing' => 'warning',
                        'published' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'reviewing' => 'Reviewing',
                        'published' => 'Published',
                    ]),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
                Actions\ForceDeleteAction::make(),
                Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    Actions\ForceDeleteBulkAction::make(),
                    Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        if (request()->query('groupedRelations') === '1') {
            return [
                RelationGroup::make('Interactions', [
                    PostResource\RelationManagers\CommentsRelationManager::class,
                    PostResource\RelationManagers\TagsRelationManager::class,
                ]),
                RelationGroup::make('Links', [
                    PostResource\RelationManagers\LinksRelationManager::class,
                ]),
            ];
        }

        return [
            'comments' => PostResource\RelationManagers\CommentsRelationManager::class,
            'tags' => PostResource\RelationManagers\TagsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'create-wizard' => Pages\CreatePostWizard::route('/create-wizard'),
            'create-header-action' => Pages\CreatePostHeaderAction::route('/create-header-action'),
            'view' => Pages\ViewPost::route('/{record}'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
            'edit-section-actions' => Pages\EditPostSectionActions::route('/{record}/edit-section-actions'),
            'edit-header-actions' => Pages\EditPostHeaderActions::route('/{record}/edit-header-actions'),
            'edit-save-in-header' => Pages\EditPostSaveInHeader::route('/{record}/edit-save-in-header'),
        ];
    }
}
