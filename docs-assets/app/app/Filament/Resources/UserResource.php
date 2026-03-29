<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use BackedEnum;
use Filament\Forms;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-users';

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        $position = request()->query('subNavPosition');

        return match ($position) {
            'end' => SubNavigationPosition::End,
            'top' => SubNavigationPosition::Top,
            default => SubNavigationPosition::Start,
        };
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        if (request()->query('noSubNav') === '1') {
            return [];
        }

        $pages = [
            Pages\ViewUser::class,
            Pages\EditUser::class,
        ];

        // Sub-nav screenshot excludes Posts to avoid confusion with relation manager screenshots
        if (request()->query('noPostsSubNav') !== '1') {
            $pages[] = Pages\ManageUserPosts::class;
        }

        $pages = array_merge($pages, [
            Pages\ManageUserComments::class,
            Pages\ManageUserOrders::class,
            Pages\ManageUserPayments::class,
            Pages\ManageUserNotifications::class,
            Pages\ManageUserSettings::class,
        ]);

        return $page->generateNavigationItems($pages);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PostsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'posts' => Pages\ManageUserPosts::route('/{record}/posts'),
            'comments' => Pages\ManageUserComments::route('/{record}/comments'),
            'orders' => Pages\ManageUserOrders::route('/{record}/orders'),
            'payments' => Pages\ManageUserPayments::route('/{record}/payments'),
            'notifications' => Pages\ManageUserNotifications::route('/{record}/notifications'),
            'settings' => Pages\ManageUserSettings::route('/{record}/settings'),
        ];
    }
}
