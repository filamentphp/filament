<?php

namespace Filament\Http\Fieldsets;

use Filament\Contracts\Fieldset;
use Filament\Fields\Input;
use Filament\Fields\Textarea;
use Filament\Fields\Checkboxes;
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

    public static function rulesIgnoreRealtime(): array
    {
        return [];
    }
} 