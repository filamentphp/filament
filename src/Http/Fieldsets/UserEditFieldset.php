<?php

namespace Filament\Http\Fieldsets;

use Filament\Http\Fields\Input;
use Filament\Fields\File\File;
use Filament\Http\Fields\Checkbox;
use Filament\Http\Fields\Checkboxes;
use Illuminate\Validation\Rule;
use Filament\Models\Role;
use Filament\Models\Permission;
use Filament\Models\Media;

class UserEditFieldset
{
    public static function name()
    {
        return 'Edit User';
    }

    public static function fields($model)
    {
        return [
            Input::make('name')
                ->rules(['required', 'string', 'max:255'])
                ->class('md:col-span-2')
                ->group('account'),
            Input::make('email')
                ->type('email')
                ->label('E-mail Address')
                ->rules([
                    'required', 
                    'string', 
                    'email', 
                    'max:255', 
                    Rule::unique('users', 'email')->ignore($model->id),
                ])
                ->class('md:col-span-2')
                ->group('account'),
            File::make('avatar')
                ->rules('array')
                ->fileRules('image')
                ->fileValidationMessages([
                    'image' => __('The Avatar must be a valid image.'),
                ])
                ->files(Media::whereIn('id', $model->avatar)->pluck('value', 'id')->all())
                ->group('account'),
            Input::make('password')
                ->type('password')
                ->label('New password')
                ->autocomplete('new-password')
                ->rules(['nullable', 'min:8', 'confirmed'])
                ->help('Leave blank to keep current password.')
                ->group('account'),
            Input::make('password_confirmation')
                ->type('password')
                ->label(false)
                ->placeholder('Confirm New Password')
                ->autocomplete('new-password')
                ->group('account'),
            Checkbox::make('is_super_admin')
                ->help(__('filament::users.super_admin_info'))
                ->group('permissions')
                ->disabled(!auth()->user()->is_super_admin),
            Checkboxes::make('roles')
                ->options(Role::orderBy('name')
                    ->pluck('id', 'name')
                    ->all())
                ->default(array_map('strval', $model->roles
                    ->pluck('id')
                    ->all()))
                ->rules([Rule::exists('roles', 'id')])
                ->group('permissions')
                ->disabled(!auth()->user()->can('edit user roles')),
            Checkboxes::make('direct_permissions')
                ->options(Permission::orderBy('name')
                    ->pluck('id', 'name')
                    ->all())
                ->default(array_map('strval', $model->getDirectPermissions()->pluck('id')->all()))
                ->rules([Rule::exists('permissions', 'id')])
                ->help(__('filament::permissions.permissions_from_roles'))
                ->group('permissions')
                ->disabled(!auth()->user()->can('edit user permissions')),
        ];
    }

    public static function rulesIgnoreRealtime()
    {
        return ['confirmed'];
    }
} 