<?php

namespace Filament\Http\Fieldsets;

use Filament\Contracts\Fieldset;
use Filament\Http\Fields\Input;
use Filament\Http\Fields\Textarea;
use Filament\Http\Fields\Checkboxes;
use Illuminate\Validation\Rule;
use Filament\Models\Role;

class PermissionCreateFieldset implements Fieldset
{
    public static function title(): string
    {
        return __('filament::permissions.create');
    }

    public static function fields($model): array
    {
        return [
            Input::make('name')
                ->rules([
                    'required', 
                    'string', 
                    'max:255', 
                    Rule::unique('permissions', 'name'),
                ])
                ->group('info'),
            Textarea::make('description')
                ->rules(['string', 'nullable'])
                ->group('info'),
            Checkboxes::make('roles')
                ->options(Role::orderBy('name')
                    ->pluck('id', 'name')
                    ->all())
                ->rules([Rule::exists('roles', 'id')])
                ->disabled(!auth()->user()->can('edit roles'))
                ->group('roles'),
        ];
    }

    public static function rulesIgnoreRealtime(): array
    {
        return [];
    }
} 