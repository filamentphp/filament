<?php

namespace Filament\Tests\Fixtures\Models;

use Filament\Tests\Database\Factories\PostMetadataFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostMetadata extends Model
{
    use HasFactory;

    protected $table = 'post_metadata';

    protected $guarded = [];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    protected static function newFactory()
    {
        return PostMetadataFactory::new();
    }
}
