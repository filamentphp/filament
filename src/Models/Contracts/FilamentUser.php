<?php

namespace Filament\Models\Contracts;

interface FilamentUser
{
    public static function getFilamentAdminColumn();

    public static function getFilamentRolesColumn();

    public static function getFilamentUserColumn();

    public function canAccessFilament();

    public function isFilamentAdmin();
}
