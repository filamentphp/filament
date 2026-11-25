<?php

namespace Filament\Tests\Fixtures\Models;

class PostWithStringKey extends Post
{
    protected $table = 'posts';

    protected $primaryKey = 'title';

    protected $keyType = 'string';

    public $incrementing = false;
}
