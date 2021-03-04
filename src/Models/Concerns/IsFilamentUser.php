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

    public static function getFilamentAvatarColumn()
    {
        if (property_exists(static::class, 'filamentAvatarColumn')) {
            return static::$filamentAvatarColumn;
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

        return $column !== null ?
            $this->{$column} :
            true;
    }

    public function hasFilamentRole($role)
    {
        $column = static::getFilamentRolesColumn();

        return $column !== null ?
            in_array($role, $this->{$column}) :
            true;
    }

    public function getFilamentAvatar()
    {
        $column = static::getFilamentAvatarColumn();

        return $column !== null ?
            $this->{$column} :
            null;
    }

    public function isFilamentAdmin()
    {
        $column = static::getFilamentAdminColumn();

        return $column !== null ?
            $this->{$column} :
            true;
    }
}
