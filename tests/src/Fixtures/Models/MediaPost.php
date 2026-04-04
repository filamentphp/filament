<?php

namespace Filament\Tests\Fixtures\Models;

use Filament\Tests\Database\Factories\MediaPostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MediaPost extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $table = 'posts';

    protected $guarded = [];

    protected static function newFactory(): MediaPostFactory
    {
        return MediaPostFactory::new();
    }
}
