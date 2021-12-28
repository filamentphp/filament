<?php

namespace Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Support\Str;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Filament\Resources\RoleResource\Pages\EditRole;
use Filament\Resources\RoleResource\Pages\ListRoles;
use Filament\Resources\RoleResource\Pages\CreateRole;
use Filament\Resources\RoleResource\RelationManagers\UsersRelationManager;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-finger-print';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Toggle::make('select_all')
                                ->onIcon('heroicon-s-shield-check')
                                ->offIcon('heroicon-s-shield-exclamation')
                                ->label('Select All')
                                ->helperText('Enable all Permissions for this role.')
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, $state) {
                                    foreach (static::getEntities() as $entity) {
                                        $set($entity, $state);
                                        foreach(static::getPermissions() as $perm)
                                        {
                                            $set($entity.'_'.$perm, $state);
                                        }
                                    }

                                })
                        ]),
                ]),
                Forms\Components\Grid::make([
                    'sm' => 2,
                    'lg' => 3,
                ])
                ->schema(static::getEntitySchema())
                ->columns([
                    'sm' => 2,
                    'lg' => 3
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // UsersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }

protected static function getEntities(): ?array
{
    return collect(array_merge(Filament::getResources(),Filament::getPages()))
        ->reduce(function ($options, $resource) {
            $option = Str::before(Str::afterLast($resource,'\\'),'Resource');
            $options[$option] = $option;
            return $options;
        }, []);
}

protected static function getPermissions(): array
{
    return ['view','viewAny','create','delete','deleteAny','update'];
}

public static function getEntitySchema()
{
    return collect(static::getEntities())->reduce(function($entities,$entity) {
            $entities[] = Forms\Components\Card::make()
                ->schema([
                    Forms\Components\Toggle::make($entity)
                        ->helperText(gettype($entity))
                        ->onIcon('heroicon-s-lock-open')
                        ->offIcon('heroicon-s-lock-closed')
                        ->reactive()
                        ->afterStateUpdated(function (Closure $set,Closure $get, $state) use($entity) {

                            collect(static::getPermissions())->each(function ($permission) use($set, $entity, $state) {
                                    $set($entity.'_'.$permission, $state);
                            });

                            if (! $state) {
                                $set('select_all',false);
                            }

                            $entityStates = [];
                            foreach(static::getEntities() as $ent) {
                                $entityStates [] = $get($ent);
                            }

                            if (in_array(false,$entityStates, true) === false) {
                                $set('select_all', true); // if all toggles on => turn select_all on
                            }

                            if (in_array(true,$entityStates, true) === false) {
                                $set('select_all', false); // if even one toggle off => turn select_all off
                            }
                        }),
                    Forms\Components\Fieldset::make('Permissions')
                    ->extraAttributes(['class' => 'text-primary-600','style' => 'border-color:var(--primary)'])
                    ->columns([
                        'default' => 2,
                        'xl' => 3
                    ])
                    ->schema(static::getPermissionsSchema($entity))
                ])
                ->columns(2)
                ->columnSpan(1);
            return $entities;
    },[]);
}

public static function getPermissionsSchema($entity)
{
    return collect(static::getPermissions())->reduce(function ($permissions, $permission) use ($entity) {
        $permissions[] = Forms\Components\Checkbox::make($entity.'_'.$permission)
                            ->label($permission)
                            ->extraAttributes(['class' => 'text-primary-600'])
                            ->reactive()
                            ->afterStateUpdated(function (Closure $set, Closure $get, $state) use($entity){

                                $permissionStates = [];
                                foreach(static::getPermissions() as $perm) {
                                    $permissionStates [] = $get($entity.'_'.$perm);
                                }

                                if (in_array(false,$permissionStates, true) === false) {
                                    $set($entity, true); // if all permissions true => turn toggle on
                                }

                                if (in_array(true,$permissionStates, true) === false) {
                                    $set($entity, false); // if even one false => turn toggle off
                                }

                                if(!$state) {
                                    $set($entity,false);
                                    $set('select_all',false);
                                }

                                $entityStates = [];
                                foreach(static::getEntities() as $ent) {
                                    $entityStates [] = $get($ent);
                                }

                                if (in_array(false,$entityStates, true) === false) {
                                    $set('select_all', true); // if all toggles on => turn select_all on
                                }

                                if (in_array(true,$entityStates, true) === false) {
                                    $set('select_all', false); // if even one toggle off => turn select_all off
                                }
                            })->dehydrated(fn(Closure $get, $state): bool => !$get($state) ? ($state ?$state:false) :true);
        return $permissions;
    },[]);
}
}
