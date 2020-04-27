<?php

namespace Filament\Http\Fieldsets;

use Filament\Contracts\Fieldset;
use Filament\Http\Fields\Input;
use Filament\Http\Fields\Textarea;
use Filament\Http\Fields\Checkboxes;
use Illuminate\Validation\Rule;
use Filament\Models\Permission;

class RoleEditFieldset implements Fieldset
{
    public static function title(): string
    {
        return __('filament::roles.edit');
    }

    public static function fields($model): array
    {
        return [
            Input::make('name')
                ->rules([
                    'required', 
                    'string', 
                    'max:255', 
                    Rule::unique('roles', 'name')->ignore($model->id),
                ])
                ->group('info'),
            Textarea::make('description')
                ->rules(['string', 'nullable'])
                ->group('info'),
            Checkboxes::make('permissions')
                ->options(Permission::orderBy('name')
                    ->pluck('id', 'name')
                    ->all())
                ->default(array_map('strval', $model->permissions
                    ->pluck('id')
                    ->all()))
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