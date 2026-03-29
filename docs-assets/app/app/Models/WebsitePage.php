<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsitePage extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_homepage' => 'boolean',
    ];
}
