<?php

namespace Filament\Tests\Fixtures\Models;

use Filament\Tests\Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\HasTags;

class Article extends Model
{
    use HasFactory;
    use HasTags;

    protected $guarded = [];

    protected static function newFactory(): ArticleFactory
    {
        return ArticleFactory::new();
    }
}
