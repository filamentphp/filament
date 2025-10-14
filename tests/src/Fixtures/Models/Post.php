<?php

namespace Filament\Tests\Fixtures\Models;

use Filament\Tests\Database\Factories\PostFactory;
use Filament\Tests\Fixtures\Enums\StringBackedEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'string_backed_enum' => StringBackedEnum::class,
            'is_published' => 'boolean',
            'tags' => 'array',
            'json' => 'array',
            'json_array_of_objects' => 'array',
            'config' => 'array',
        ];
    }

    protected $guarded = [];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function config(string $key): mixed
    {
        return $this->config[$key] ?? null;
    }

    protected static function newFactory()
    {
        return PostFactory::new();
    }
}
