<?php

namespace Filament;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;

class SpatieLaravelPermissionPlugin
{
    public static function generateFor(string $model): void
    {
        $permissions = collect();
        cache::forget('spatie.permission.cache');
        collect(config('filament-spatie-laravel-permission-plugin.default_permission_prefixes'))
            ->unique()
            ->each(function($prefix) use($model, $permissions){
                $permissions->push(Permission::create([
                    'name' => $prefix.'_'.$model
                ]));
            });
        logger($permissions);
        if (static::superAdminIsEnabled()) {
            $superAdmin = Role::firstOrCreate([
                'name' => config('filament-spatie-laravel-permission-plugin.default_roles.super_admin.role_name')
            ]);
            $superAdmin->givePermissionTo($permissions);
        }
    }

    protected static function superAdminIsEnabled(): bool
    {
        return config('filament-spatie-laravel-permission-plugin.default_roles.super_admin.enabled');
    }
}
