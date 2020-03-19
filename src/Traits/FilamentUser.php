<?php

namespace Filament\Traits;

use Filament\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Thomaswelton\LaravelGravatar\Facades\Gravatar;

trait FilamentUser 
{    
    /**
     * Initialize the trait.
     * 
     * @return void
     */
    public function initializeFilamentUser()
    {
        $this->mergeFillable(['is_super_admin', 'avatar']);
        $this->mergeCasts(['is_super_admin' => 'boolean']);
    }

    /**
     * Get the user's avatar.
     * 
     * @param int $size
     * @return string
     */
    public function avatar($size = 48)
    {
        return Gravatar::src($this->email, (int) $size);
    }

    /**
     * Merge fillable attributes.
     * 
     * @return void
     */
    protected function mergeFillable(array $fillable)
    {
        $this->fillable(collect($this->fillable)->merge($fillable)->all());
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Hash the users password.
     *
     * @param  string  $pass
     * @return void
     */
    public function setPasswordAttribute($pass)
    {
        $this->attributes['password'] = Hash::make($pass);
    }
}