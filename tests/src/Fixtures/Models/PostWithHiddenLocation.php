<?php

namespace Filament\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

class PostWithHiddenLocation extends Model
{
    protected $table = 'posts';

    /**
     * @var array<string>
     */
    protected $hidden = ['location'];
}
