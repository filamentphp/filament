<?php

namespace Filament\Tests\Models;

use Filament\Tests\Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $casts = [
        'is_published' => 'boolean',
        'tags' => 'array',
    ];

    protected $guarded = [];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    protected static function newFactory()
    {
        return CommentFactory::new();
    }
}
