<?php

namespace Filament\Http\Fieldsets;

use Filament\Contracts\Fieldset;
use Filament\Support\Fields\Field;
use Illuminate\Validation\Rule;
use Filament\Models\Permission;

class RoleCreateFieldset implements Fieldset
{
    public static function title(): string
    {
        return __('filament::permissions.roles.create');
    }

    public static function fields($model): array
    {
        return [
            Field::make('name')
                ->input()
                ->rules([
                    'required', 
                    'string', 
                    'max:255', 
                    Rule::unique('roles', 'name'),
                ])
                ->group('info'),
            Field::make('description')
                ->textarea()
                ->rules(['string', 'nullable'])
                ->group('info'),
            Field::make('permissions')
                ->checkboxes(Permission::orderBy('name')
                    ->pluck('id', 'name')
                    ->all())
                ->rules([Rule::exists('permissions', 'id')])
                ->group('permissions'),
        ];
    }

    public static function rulesIgnoreRealtime(): array
    {
        return [];
    }
} 