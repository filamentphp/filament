<?php

namespace Filament\Http\Fieldsets;

use Filament\Contracts\Fieldset;
use Filament\Support\Fields\Field;
use Illuminate\Validation\Rule;
// use Filament\Models\Permission;

class RoleEditFieldset implements Fieldset
{
    public static function title(): string
    {
        return 'Role';
    }

    public static function fields($model): array
    {
        return [
            Field::make('Name')
                ->input()
                ->rules([
                    'required', 
                    'string', 
                    'max:255', 
                    Rule::unique('roles', 'name')->ignore($model->id),
                ]),
            Field::make('Description')
                ->textarea()
                ->rules(['string'])
        ];
    }

    public static function rulesIgnoreRealtime(): array
    {
        return [];
    }
} 