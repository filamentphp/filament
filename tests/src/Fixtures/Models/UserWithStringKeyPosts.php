<?php

namespace Filament\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class UserWithStringKeyPosts extends User
{
    protected $table = 'users';

    public function posts(): HasMany
    {
        return $this->hasMany(PostWithStringKey::class, 'author_id');
    }
}
