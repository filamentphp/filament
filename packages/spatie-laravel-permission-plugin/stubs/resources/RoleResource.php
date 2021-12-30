<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Support\Str;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Filament\Resources\RoleResource\Pages;

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
                                        foreach(config('filament-spatie-laravel-permission-plugin.default_permission_prefixes') as $perm)
                                        {
                                            $set($perm.$entity, $state);
                                        }
                                    }

                                })
                                ->dehydrated(fn($state):bool => $state?:false)
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    protected static function getEntities(): ?array
    {
        return Permission::pluck('name')
            ->reduce(function ($roles, $permission) {
                $roles->push(Str::afterLast($permission, '_'));
                return $roles->unique();
            },collect())
            ->toArray();
    }

    public static function getEntitySchema()
    {
        return collect(static::getEntities())->reduce(function($entities,$entity) {
                $entities[] = Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Toggle::make($entity)
                            ->onIcon('heroicon-s-lock-open')
                            ->offIcon('heroicon-s-lock-closed')
                            ->reactive()
                            ->afterStateUpdated(function (Closure $set,Closure $get, $state) use($entity) {

                                collect(config('filament-spatie-laravel-permission-plugin.default_permission_prefixes'))->each(function ($permission) use($set, $entity, $state) {
                                        $set($permission.'_'.$entity, $state);
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
                            })
                            ->dehydrated(false)
                            ,
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
        return collect(config('filament-spatie-laravel-permission-plugin.default_permission_prefixes'))->reduce(function ($permissions, $permission) use ($entity) {
            $permissions[] = Forms\Components\Checkbox::make($permission.'_'.$entity)
                                ->label(Str::studly($permission))
                                ->extraAttributes(['class' => 'text-primary-600'])
                                ->afterStateHydrated(function (Closure $set, Closure $get, $record) use($entity, $permission) {
                                    if (is_null($record)) return;

                                    // sit the state for the existing permissions
                                    $existingPermissions = $record->permissions->pluck('name')->reduce(function ($permissions, $permission){
                                        $permissions[$permission] = true;
                                        return $permissions;
                                    },[]);

                                    $set($permission.'_'.$entity, in_array($permission.'_'.$entity,array_keys($existingPermissions)) && $existingPermissions[$permission.'_'.$entity] ? true : false);
                                    // check if any entites' one or all are true
                                    $entities = $record->permissions->pluck('name')
                                        ->reduce(function ($roles, $role){
                                            $roles[$role] = Str::afterLast($role, '_');
                                            return $roles;
                                        },collect())
                                        ->values()
                                        ->groupBy(function ($item) {
                                            return $item;
                                        })->map->count()
                                        ->reduce(function ($counts,$role,$key) {
                                            if ($role === 6) {
                                                $counts[$key] = true;
                                            }else {
                                                $counts[$key] = false;
                                            }
                                            return $counts;
                                        },[]);

                                    // set entity's state if one are all permissions are true
                                    if (in_array($entity,array_keys($entities)) && $entities[$entity])
                                    {
                                        $set($entity, true);
                                    } else {
                                        $set($entity, false);
                                        $set('select_all', false);
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
                                })
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, Closure $get, $state) use($entity){

                                    $permissionStates = [];
                                    foreach(config('filament-spatie-laravel-permission-plugin.default_permission_prefixes') as $perm) {
                                        $permissionStates [] = $get($perm.$entity);
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
                                })
                                ->dehydrated(fn($state): bool => $state?:false);
            return $permissions;
        },[]);
    }
}
