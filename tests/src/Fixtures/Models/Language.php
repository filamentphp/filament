<?php

namespace Filament\Tests\Fixtures\Models;

use Filament\Tests\Database\Factories\LanguageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected static function newFactory()
    {
        return LanguageFactory::new();
    }
}
