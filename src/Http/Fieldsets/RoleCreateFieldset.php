<?php

namespace Filament\Http\Fieldsets;

use Filament\Contracts\Fieldset;
use Filament\Fields\Input;
use Filament\Fields\Textarea;
use Filament\Fields\Checkboxes;
use Illuminate\Validation\Rule;
use Filament\Models\Permission;

class RoleCreateFieldset implements Fieldset
{
    public static function title(): string
    {
        return __('filament::roles.create');
    }

    public static function fields($model): array
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

    public static function rulesIgnoreRealtime(): array
    {
        return [];
    }
} 