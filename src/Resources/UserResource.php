<?php

namespace Filament\Resources;

use Filament\Models\FilamentUser;
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;
use Filament\Resources\Tables\Columns;
use Filament\Resources\Tables\Filter;
use Filament\Resources\Tables\Table;
use Filament\Resources\UserResource\Pages;

class UserResource extends Resource
{
    public static $icon = 'heroicon-o-user-group';

    public static $label = 'user';

    public static $model = FilamentUser::class;

    public static $routeNamePrefix = 'filament';

    public static $slug = 'users';

    public static function form(Form $form)
    {
        return $form
            ->schema([
                Components\Grid::make([
                    Components\TextInput::make('name')
                        ->disableAutocomplete()
                        ->required(),
                    Components\TextInput::make('email')
                        ->email()
                        ->disableAutocomplete()
                        ->required()
                        ->unique(FilamentUser::class, 'email', true),
                ]),
                Components\Fieldset::make('Password', [
                    Components\TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->autocomplete('new-password')
                        ->confirmed()
                        ->minLength(8)
                        ->required(),
                    Components\TextInput::make('passwordConfirmation')
                        ->label('Confirm password')
                        ->password()
                        ->autocomplete('new-password')
                        ->required(),
                ])->only(Pages\CreateUser::class),
                Components\Fieldset::make('Set a new password', [
                    Components\TextInput::make('newPassword')
                        ->label('Password')
                        ->password()
                        ->autocomplete('new-password')
                        ->confirmed()
                        ->minLength(8),
                    Components\TextInput::make('newPasswordConfirmation')
                        ->label('Confirm password')
                        ->password()
                        ->autocomplete('new-password')
                        ->requiredWith('newPassword'),
                ])->only(Pages\EditUser::class),
                Components\Grid::make([
                    Components\FileUpload::make('avatar')
                        ->avatar()
                        ->directory('filament-avatars')
                        ->disk(config('filament.default_filesystem_disk')),
                    Components\Checkbox::make('is_admin')->label('Administrator?'),
                ]),
            ]);
    }

    public static function navigationItems()
    {
        return [];
    }

    public static function table(Table $table)
    {
        return $table
            ->columns([
                Columns\Text::make('name')
                    ->primary()
                    ->searchable()
                    ->sortable(),
                Columns\Text::make('email')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($user) => "mailto:{$user->email}"),
            ])
            ->filters([
                Filter::make('administrators', fn ($query) => $query->where('is_admin', true)),
            ]);
    }

    public static function routes()
    {
        return [
            Pages\ListUsers::routeTo('/', 'index'),
            Pages\CreateUser::routeTo('/create', 'create'),
            Pages\EditUser::routeTo('/{record}/edit', 'edit'),
        ];
    }
}
