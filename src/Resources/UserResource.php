<?php

namespace Filament\Resources;

use Filament\Filament;
use Filament\Models\User;
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\Form;
use Filament\Resources\Tables\Columns;
use Filament\Resources\Tables\Filter;
use Filament\Resources\Tables\Table;
use Filament\Resources\UserResource\Pages;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    public static $icon = 'heroicon-o-user-group';

    public static $model = User::class;

    public static $routeNamePrefix = 'filament';

    public static function form(Form $form)
    {
        return $form
            ->schema([
                Components\Grid::make([
                    Components\TextInput::make('name')
                        ->label('filament::resources/user-resource.form.name.label')
                        ->disableAutocomplete()
                        ->required(),
                    Components\TextInput::make('email')
                        ->label('filament::resources/user-resource.form.email.label')
                        ->email()
                        ->disableAutocomplete()
                        ->required()
                        ->unique(User::class, 'email', true),
                ]),
                Components\Fieldset::make('Password', [
                    Components\TextInput::make('password')
                        ->label('filament::resources/user-resource.form.password.label')
                        ->password()
                        ->autocomplete('new-password')
                        ->confirmed()
                        ->minLength(8)
                        ->required(),
                    Components\TextInput::make('passwordConfirmation')
                        ->label('filament::resources/user-resource.form.passwordConfirmation.label')
                        ->password()
                        ->autocomplete('new-password')
                        ->required(),
                ])->only(Pages\CreateUser::class),
                Components\Fieldset::make('filament::resources/user-resource.form.newPassword.fieldset.label', [
                    Components\TextInput::make('newPassword')
                        ->label('filament::resources/user-resource.form.newPassword.fields.newPassword.label')
                        ->password()
                        ->autocomplete('new-password')
                        ->confirmed()
                        ->minLength(8),
                    Components\TextInput::make('newPasswordConfirmation')
                        ->label('filament::resources/user-resource.form.newPassword.fields.newPasswordConfirmation.label')
                        ->password()
                        ->autocomplete('new-password')
                        ->requiredWith('newPassword'),
                ])->only(Pages\EditUser::class),
                Components\Grid::make([
                    Components\MultiSelect::make('roles')
                        ->label('filament::resources/user-resource.form.roles.label')
                        ->placeholder('Select a role')
                        ->options(
                            collect(Filament::getRoles())
                                ->mapWithKeys(fn ($role) => [$role => Str::ucfirst($role::getLabel())])
                                ->toArray(),
                        ),
                    Components\Checkbox::make('is_admin')
                        ->label('filament::resources/user-resource.form.isAdmin.label'),
                ]),
                Components\FileUpload::make('avatar')
                    ->label('filament::resources/user-resource.form.avatar.label')
                    ->avatar()
                    ->directory('filament-avatars')
                    ->disk(config('filament.default_filesystem_disk')),
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
                    ->label('filament::resources/user-resource.table.columns.name.label')
                    ->primary()
                    ->searchable()
                    ->sortable(),
                Columns\Text::make('email')
                    ->label('filament::resources/user-resource.table.columns.email.label')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($user) => "mailto:{$user->email}"),
            ])
            ->filters([
                Filter::make('administrators', fn ($query) => $query->where('is_admin', true))
                    ->label('filament::resources/user-resource.table.filters.administrators.label'),
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
