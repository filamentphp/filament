<?php

namespace Alpine\Traits;

use Alpine\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;

trait AlpineUser 
{    
    /**
     * Initialize the trait.
     * 
     * @return void
     */
    public function initializeAlpineUser()
    {
        $this->mergeFillable(['is_super_admin']);
        $this->mergeCasts(['is_super_admin' => 'boolean']);
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