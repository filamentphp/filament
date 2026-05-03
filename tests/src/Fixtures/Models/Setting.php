<?php

namespace Filament\Tests\Fixtures\Models;

use Filament\Tests\Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function languageWithTrashed(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id')->withTrashed();
    }

    protected static function newFactory()
    {
        return SettingFactory::new();
    }
}
