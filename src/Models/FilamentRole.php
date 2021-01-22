<?php

namespace Filament\Models;

use Filament\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use ReflectionClass;
use Sushi\Sushi;
use Symfony\Component\Finder\SplFileInfo;

class FilamentRole extends Model
{
    use Sushi;

    public function getRows()
    {
        return collect((new Filesystem())->allFiles(app_path('Filament/Roles')))
            ->map(function (SplFileInfo $file) {
                return (string) Str::of('App\\Filament\\Roles')
                    ->append('\\', $file->getRelativePathname())
                    ->replace(['/', '.php'], ['\\', '']);
            })
            ->filter(function ($class) {
                return is_subclass_of($class, Role::class) && ! (new ReflectionClass($class))->isAbstract();
            })
            ->map(function ($role) {
                return [
                    'id' => $role,
                    'label' => $role::getLabel(),
                ];
            })
            ->toArray();
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
