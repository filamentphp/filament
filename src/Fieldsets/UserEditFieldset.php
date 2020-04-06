<?php

namespace Filament\Fieldsets;

use Filament\Contracts\Fieldset;
use Filament\Support\Fields\Field;
use Illuminate\Validation\Rule;
use Spatie\Permission\Contracts\Role as RoleContract;

class UserEditFieldset implements Fieldset
{
    public static function title(): string
    {
        return 'User';
    }

    public static function fields($model): array
    {
        return [
            Field::make('Name')
                ->input()
                ->rules(['required', 'string', 'max:255'])
                ->group('account'),
            Field::make('Email')
                ->input('email')
                ->rules([
                    'required', 
                    'string', 
                    'email', 
                    'max:255', 
                    Rule::unique('users', 'email')->ignore($model->id),
                ])
                ->group('account'),
            Field::make('Avatar')
                ->rules('array')
                ->file()
                ->fileRules('image')
                ->fileValidationMessages([
                    'image' => __('The Avatar must be a valid image.'),
                ])
                ->group('account'),
            Field::make('Password')
                ->input('password')
                ->autocomplete('new-password')
                ->rules(['sometimes', 'confirmed'])
                ->help('Leave blank to keep current password.')
                ->class('md:col-span-2')
                ->group('account'),
            Field::make('Confirm Password', 'password_confirmation')
                ->input('password')
                ->autocomplete('new-password')
                ->class('md:col-span-2')
                ->group('account'),
            Field::make('filament::permissions.super_admin', 'is_super_admin')
                ->checkbox()
                ->help(__('filament::permissions.super_admin_info'))
                ->group('permissions'),
            Field::make('filament::permissions.roles', 'roles')
                ->checkboxes(app(RoleContract::class)::orderBy('name')
                    ->pluck('id', 'name')
                    ->all())
                ->default(array_map('strval', $model->roles
                    ->pluck('id')
                    ->all()))
                ->rules([Rule::exists('roles', 'id')])
                ->group('permissions'),
        ];
    }

    public static function rulesIgnoreRealtime()
    {
        return ['confirmed'];
    }
} 