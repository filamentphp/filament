<?php

namespace Filament\Http\Fieldsets;

use Filament\Contracts\Fieldset;
use Filament\Http\Fields\Input;
use Filament\Http\Fields\Checkbox;
use Filament\Http\Fields\Checkboxes;
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
            Input::make('name')
                ->rules(['required', 'string', 'max:255'])
                ->group('account'),
            Input::make('email')
                ->type('email')
                ->rules([
                    'required', 
                    'string', 
                    'email', 
                    'max:255', 
                    Rule::unique('users', 'email'),
                ])
                ->group('account'),
            Input::make('password')
                ->type('password')
                ->autocomplete('new-password')
                ->rules(['required', 'min:8', 'confirmed'])
                ->group('account'),
            Input::make('password_confirmation', false)
                ->type('password')
                ->placeholder('Confirm Password')
                ->autocomplete('new-password')
                ->rules('required')
                ->group('account'),
            Checkbox::make('is_super_admin')
                ->default(false)
                ->help(__('filament::users.super_admin_info'))
                ->group('permissions')
                ->disabled(!auth()->user()->is_super_admin)
                ->group('permissions'),
            Checkboxes::make('roles')
                ->options(Role::orderBy('name')->pluck('id', 'name')->all())
                ->rules([Rule::exists('roles', 'id')])
                ->disabled(!auth()->user()->can('edit user roles'))
                ->group('permissions'),
            Checkboxes::make('direct_permissions')
                ->options(Permission::orderBy('name')
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