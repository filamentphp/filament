<?php

namespace Filament\Models\Concerns;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Support\Facades\URL;

trait IsFilamentUser
{
    public function canAccessFilament()
    {
        $column = static::getFilamentUserColumn();

        return $column !== null ?
            $this->{$column} :
            true;
    }

    public static function getFilamentAdminColumn()
    {
        if (property_exists(static::class, 'filamentAdminColumn')) {
            return static::$filamentAdminColumn;
        }

        return null;
    }

    public function getFilamentAvatar()
    {
        $column = static::getFilamentAvatarColumn();

        return $column !== null ?
            $this->{$column} :
            null;
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

    public function hasFilamentRole($role)
    {
        $column = static::getFilamentRolesColumn();

        return $column !== null ?
            in_array($role, $this->{$column}) :
            true;
    }

    public function isFilamentAdmin()
    {
        $column = static::getFilamentAdminColumn();

        return $column !== null ?
            $this->{$column} :
            false;
    }

    public function sendPasswordResetNotification($token)
    {
        $notification = new ResetPasswordNotification($token);
        $notification->createUrlUsing(function ($notifiable, $token) {
            return URL::signedRoute(
                'filament.auth.password.reset',
                [
                    'email' => $notifiable->getEmailForPasswordReset(),
                    'token' => $token,
                ],
                now()->addMinutes(config('auth.passwords.filament_users.expire')),
            );
        });

        $this->notify($notification);
    }
}
