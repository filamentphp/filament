<?php

namespace Filament\Http\Fieldsets;

use Filament\Contracts\Fieldset;
use Filament\Support\Fields\Field;
use Illuminate\Validation\Rule;
use Filament\Models\Role;
use Filament\Models\Permission;

class UserCreateFieldset implements Fieldset
{
    public static function title(): string
    {
        return 'New User';
    }

    public static function fields($model): array
    {
        return [
            Field::make('name')
                ->input()
                ->rules(['required', 'string', 'max:255'])
                ->group('account'),
            Field::make('email')
                ->input('email')
                ->rules([
                    'required', 
                    'string', 
                    'email', 
                    'max:255', 
                    Rule::unique('users', 'email'),
                ])
                ->group('account'),
            Field::make('password')
                ->input('password')
                ->autocomplete('new-password')
                ->rules(['required', 'min:8', 'confirmed'])
                ->group('account'),
            Field::make('password_confirmation', false)
                ->placeholder('Confirm Password')
                ->input('password')
                ->autocomplete('new-password')
                ->rules('required')
                ->group('account'),
            Field::make('is_super_admin')
                ->checkbox()
                ->default(false)
                ->help(__('filament::users.super_admin_info'))
                ->group('permissions')
                ->disabled(!auth()->user()->is_super_admin)
                ->group('permissions'),
            Field::make('roles')
                ->checkboxes(Role::orderBy('name')
                    ->pluck('id', 'name')
                    ->all())
                ->rules([Rule::exists('roles', 'id')])
                ->disabled(!auth()->user()->can('edit user roles'))
                ->group('permissions'),
            Field::make('direct_permissions')
                ->checkboxes(Permission::orderBy('name')
                    ->pluck('id', 'name')
                    ->all())
                ->rules([Rule::exists('permissions', 'id')])
                ->help(__('filament::permissions.permissions_from_roles'))
                ->disabled(!auth()->user()->can('edit user permissions'))
                ->group('permissions'),
        ];
    }

    public static function rulesIgnoreRealtime(): array
    {
        return ['confirmed'];
    }
} 