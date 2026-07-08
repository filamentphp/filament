<?php

namespace Filament\Tests\Fixtures\Models;

use RuntimeException;
use Spatie\Tags\Tag;

/**
 * A `Tag` model whose lookup throws, used to prove that the Spatie tags bulk actions report a
 * graceful failure when resolving the entered tags fails, instead of letting the exception escape
 * as an uncaught error. Wire it up with `config(['tags.tag_model' => ThrowingTag::class])`.
 */
class ThrowingTag extends Tag
{
    protected $table = 'tags';

    public static function findFromStringOfAnyType(string $name, ?string $locale = null)
    {
        throw new RuntimeException('Simulated tag resolution failure.');
    }
}
