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
        return 'Create Role';
    }

    public static function fields($model = null): array
    {
        return [
            Field::make('Name')
                ->input()
                ->rules([
                    'required', 
                    'string', 
                    'max:255', 
                    Rule::unique('roles', 'name'),
                ])
                ->group('info'),
            Field::make('Description')
                ->textarea()
                ->rules(['string', 'nullable'])
                ->group('info'),
            Field::make('filament::admin.permissions', 'permissions')
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