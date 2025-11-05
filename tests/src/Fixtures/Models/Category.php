<?php

namespace Filament\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $guarded = [];

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->with('children')->orderBy('position');
    }
}
