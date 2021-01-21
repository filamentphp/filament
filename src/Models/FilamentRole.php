<?php

namespace Filament\Models;

use Filament\Filament;
use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class FilamentRole extends Model
{
    use Sushi;

    public function getRows()
    {
        return Filament::roles()->map(function ($role) {
            return [
                'id' => $role,
                'label' => $role::getLabel(),
            ];
        })->toArray();
    }

    public function users()
    {
        return $this->belongsToMany(
            FilamentUser::class,
            'filament_role_user',
            'user_id',
            'role_id'
        );
    }
}
