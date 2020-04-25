<?php

namespace Filament\Http\Fieldsets;

use Filament\Contracts\Fieldset;
use Filament\Support\Fields\Field;
use Illuminate\Validation\Rule;
use Filament\Models\Role;

class PermissionEditFieldset implements Fieldset
{
    public static function title(): string
    {
        return __('filament::permissions.edit');
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
                    Rule::unique('permissions', 'name')->ignore($model->id),
                ])
                ->group('info'),
            Field::make('description')
                ->textarea()
                ->rules(['string', 'nullable'])
                ->group('info'),
            Field::make('roles')
                ->checkboxes(Role::orderBy('name')
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

    public static function rulesIgnoreRealtime(): array
    {
        return [];
    }
} 