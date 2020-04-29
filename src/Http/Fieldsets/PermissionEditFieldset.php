<?php

namespace Filament\Http\Fieldsets;

use Filament\Http\Fields\Input;
use Filament\Http\Fields\Textarea;
use Filament\Http\Fields\Checkboxes;
use Illuminate\Validation\Rule;
use Filament\Models\Role;

class PermissionEditFieldset
{
    public static function name()
    {
        return __('filament::permissions.edit');
    }

    public static function fields($model)
    {
        return [
            Input::make('name')
                ->rules([
                    'required', 
                    'string', 
                    'max:255', 
                    Rule::unique('permissions', 'name')->ignore($model->id),
                ])
                ->group('info'),
            Textarea::make('description')
                ->rules(['string', 'nullable'])
                ->group('info'),
            Checkboxes::make('roles')
                ->options(Role::orderBy('name')
                    ->pluck('id', 'name')
                    ->all())
                ->default(array_map('strval', $model->roles
                    ->pluck('id')
                    ->all()))
                ->rules([Rule::exists('roles', 'id')])
                ->disabled(!auth()->user()->can('edit roles'))
                ->group('roles'),
        ];
    }
} 