<?php

namespace Filament\Http\Fieldsets;

use Filament\Http\Fields\Input;
use Filament\Http\Fields\Textarea;
use Filament\Http\Fields\Checkboxes;
use Illuminate\Validation\Rule;
use Filament\Models\Permission;

class RoleCreateFieldset
{
    public static function name()
    {
        return __('filament::roles.create');
    }

    public static function fields()
    {
        return [
            Input::make('name')
                ->rules([
                    'required', 
                    'string', 
                    'max:255', 
                    Rule::unique('roles', 'name'),
                ])
                ->group('info'),
            Textarea::make('description')
                ->rules(['string', 'nullable'])
                ->group('info'),
            Checkboxes::make('permissions')
                ->options(Permission::orderBy('name')
                    ->pluck('id', 'name')
                    ->all())
                ->rules([Rule::exists('permissions', 'id')])
                ->disabled(!auth()->user()->can('edit permissions'))
                ->group('permissions'),
        ];
    }
} 