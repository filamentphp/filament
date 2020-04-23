<?php

namespace Filament\Http\Fieldsets;

use Filament\Contracts\Fieldset;
use Filament\Support\Fields\Field;
use Illuminate\Validation\Rule;
use Filament\Models\Role;
use Filament\Models\Permission;
use Filament\Models\Media;

class UserEditFieldset implements Fieldset
{
    public static function title(): string
    {
        return 'Edit User';
    }

    public static function fields($model): array
    {
        return [
            Field::make('name')
                ->input()
                ->rules(['required', 'string', 'max:255'])
                ->class('md:col-span-2')
                ->group('account'),
            Field::make('email', 'E-mail Address')
                ->input('email')
                ->rules([
                    'required', 
                    'string', 
                    'email', 
                    'max:255', 
                    Rule::unique('users', 'email')->ignore($model->id),
                ])
                ->class('md:col-span-2')
                ->group('account'),
            Field::make('avatar')
                ->rules('array')
                ->file()
                ->fileRules('image')
                ->fileValidationMessages([
                    'image' => __('The Avatar must be a valid image.'),
                ])
                ->files(Media::whereIn('id', $model->avatar)->pluck('value', 'id')->all())
                // ->multiple()
                ->group('account'),
            Field::make('password', 'New password')
                ->input('password')
                ->autocomplete('new-password')
                ->rules(['nullable', 'min:8', 'confirmed'])
                ->help('Leave blank to keep current password.')
                ->group('account'),
            Field::make('password_confirmation', false)
                ->placeholder('Confirm New Password')
                ->input('password')
                ->autocomplete('new-password')
                ->group('account'),
            Field::make('is_super_admin')
                ->checkbox()
                ->help(__('filament::users.super_admin_info'))
                ->group('permissions')
                ->disabled(!auth()->user()->is_super_admin),
            Field::make('roles')
                ->checkboxes(Role::orderBy('name')
                    ->pluck('id', 'name')
                    ->all())
                ->default(array_map('strval', $model->roles
                    ->pluck('id')
                    ->all()))
                ->rules([Rule::exists('roles', 'id')])
                ->group('permissions')
                ->disabled(!auth()->user()->can('edit user roles')),
            Field::make('direct_permissions')
                ->checkboxes(Permission::orderBy('name')
                    ->pluck('id', 'name')
                    ->all())
                ->default(array_map('strval', $model->getDirectPermissions()
                    ->pluck('id')
                    ->all()))
                ->rules([Rule::exists('permissions', 'id')])
                ->help(__('filament::permissions.permissions_from_roles'))
                ->group('permissions')
                ->disabled(!auth()->user()->can('edit user permissions')),
        ];
    }

    public static function rulesIgnoreRealtime(): array
    {
        return ['confirmed'];
    }
} 