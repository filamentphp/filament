<?php

namespace Filament\Tests\Fixtures\Resources\Users\Resources;

use BackedEnum;
use Filament\Forms;
use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Resources\Users\Resources\ManagedUserPostResource\Pages;
use Filament\Tests\Fixtures\Resources\Users\UserResource;

class ManagedUserPostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentText;

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return UserResource::asParent(static::class)
            ->relationship('posts')
            ->inverseRelationship('author');
    }

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                Forms\Components\TextInput::make('title')->required(),
                Forms\Components\MarkdownEditor::make('content'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'create' => Pages\CreateManagedUserPost::route('/create'),
            'edit' => Pages\EditManagedUserPost::route('/{record}/edit'),
        ];
    }
}
