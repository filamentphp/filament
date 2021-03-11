<?php

namespace Filament\Resources;

use Filament\Filament;
use Filament\Http\Livewire\EditAccount;
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

    public static $routeNamePrefix = 'filament';

    public static $slug = 'users';

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
                        ->unique(static::getModel(), 'email', true),
                ]),
                Components\Fieldset::make('filament::resources/user-resource.form.password.fieldset.label.edit', [
                    Components\TextInput::make('password')
                        ->label('filament::resources/user-resource.form.password.fields.password.label')
                        ->password()
                        ->autocomplete('new-password')
                        ->confirmed()
                        ->minLength(8)
                        ->only(Pages\CreateUser::class, fn ($field) => $field->required()),
                    Components\TextInput::make('passwordConfirmation')
                        ->label('filament::resources/user-resource.form.password.fields.passwordConfirmation.label')
                        ->password()
                        ->autocomplete('new-password')
                        ->only(Pages\CreateUser::class, fn ($field) => $field->required())
                        ->only([
                            EditAccount::class,
                            Pages\EditUser::class,
                        ], fn ($field) => $field->requiredWith('password')),
                ])->only(
                    Pages\CreateUser::class,
                    fn ($fieldset) => $fieldset->label('filament::resources/user-resource.form.password.fieldset.label.create'),
                ),
                Components\Grid::make(function () {
                    $schema = [];

                    $userColumn = Filament::auth()->getProvider()->getModel()::getFilamentUserColumn();
                    if ($userColumn !== null) {
                        $schema[] = Components\Checkbox::make($userColumn)
                            ->label('filament::resources/user-resource.form.isUser.label')
                            ->except(EditAccount::class);
                    }

                    $adminColumn = Filament::auth()->getProvider()->getModel()::getFilamentAdminColumn();
                    if ($adminColumn !== null) {
                        $schema[] = Components\Checkbox::make($adminColumn)
                            ->label('filament::resources/user-resource.form.isAdmin.label')
                            ->helpMessage('filament::resources/user-resource.form.isAdmin.helpMessage')
                            ->except(EditAccount::class);
                    }

                    $rolesColumn = Filament::auth()->getProvider()->getModel()::getFilamentRolesColumn();
                    if ($rolesColumn !== null) {
                        $schema[] = Components\MultiSelect::make($rolesColumn)
                            ->label('filament::resources/user-resource.form.roles.label')
                            ->placeholder('filament::resources/user-resource.form.roles.placeholder')

                            ->options(
                                collect(Filament::getRoles())
                                    ->mapWithKeys(fn ($role) => [$role => Str::ucfirst($role::getLabel())])
                                    ->toArray(),
                            )
                            ->except(EditAccount::class);
                    }

                    $avatarColumn = Filament::auth()->getProvider()->getModel()::getFilamentAvatarColumn();
                    if ($avatarColumn !== null) {
                        $schema[] = Components\FileUpload::make('avatar')
                            ->label('filament::resources/user-resource.form.avatar.label')
                            ->avatar()
                            ->directory('filament-avatars')
                            ->disk(config('filament.default_filesystem_disk'));
                    }

                    return $schema;
                }),
            ]);
    }

    public static function getModel()
    {
        return Filament::auth()->getProvider()->getModel();
    }

    public static function navigationItems()
    {
        if (static::getModel()::getFilamentUserColumn() === null) {
            return [];
        }

        return parent::navigationItems();
    }

    public static function table(Table $table)
    {
        $table->columns([
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
        ])->filters(function () {
            $filters = [];

            $adminColumn = Filament::auth()->getProvider()->getModel()::getFilamentAdminColumn();
            if ($adminColumn !== null) {
                $filters[] = Filter::make('administrators', fn ($query) => $query->where($adminColumn, true))
                    ->label('filament::resources/user-resource.table.filters.administrators.label');
            }

            return $filters;
        });

        return $table;
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
