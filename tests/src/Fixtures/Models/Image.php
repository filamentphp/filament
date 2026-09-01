<?php

namespace Filament\Tests\Fixtures\Models;

use Filament\Tests\Database\Factories\ImageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function newFactory()
    {
        return ImageFactory::new();
    }
}
