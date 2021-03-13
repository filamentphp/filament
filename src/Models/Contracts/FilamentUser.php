<?php

namespace Filament\Models\Contracts;

interface FilamentUser
{
    public function canAccessFilament();

    public static function getFilamentAdminColumn();

    public static function getFilamentRolesColumn();

    public static function getFilamentUserColumn();

    public function isFilamentAdmin();
}
