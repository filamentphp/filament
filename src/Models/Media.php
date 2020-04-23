<?php

namespace Filament\Models;

use Illuminate\Database\Eloquent\Model;
use Filament\Contracts\User as UserContract;

class Media extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'media';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'array',
    ];

    /**
     * Get the owning mediable model.
     */
    public function mediable()
    {
        return $this->morphTo();
    }

    /**
     * Get all of the users that are assigned this media.
     */
    public function users()
    {
        return $this->morphedByMany(app(UserContract::class), 'mediable');
    }
}