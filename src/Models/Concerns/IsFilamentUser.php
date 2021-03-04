<?php

namespace Filament\Models\Concerns;

trait IsFilamentUser
{
    public static function getFilamentAdminColumn()
    {
        if (property_exists(static::class, 'filamentAdminColumn')) {
            return static::$filamentAdminColumn;
        }

        return null;
    }

    public static function getFilamentRolesColumn()
    {
        if (property_exists(static::class, 'filamentRolesColumn')) {
            return static::$filamentRolesColumn;
        }

        return null;
    }

    public static function getFilamentUserColumn()
    {
        if (property_exists(static::class, 'filamentUserColumn')) {
            return static::$filamentUserColumn;
        }

        return null;
    }

    public function canAccessFilament()
    {
        $column = static::getFilamentUserColumn();

        return $column ?
            $this->{$column} :
            true;
    }

    public function hasRole($role)
    {
        $column = static::getFilamentRolesColumn();

        return $column ?
            in_array($role, $this->{$column}) :
            true;
    }

    public function isFilamentAdmin()
    {
        $column = static::getFilamentAdminColumn();

        return $column ?
            $this->{$column} :
            true;
    }
}
