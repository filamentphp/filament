<?php

namespace Filament\Tests\Admin\Fixtures\Resources;

use Filament\Resources\Resource;
use Filament\Tests\Models\PostCategory;

class PostCategoryResource extends Resource
{
    protected static ?string $model = PostCategory::class;
}
