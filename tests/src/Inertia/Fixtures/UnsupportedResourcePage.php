<?php

namespace Filament\Tests\Inertia\Fixtures;

use Filament\Resources\Pages\ListRecords;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;

class UnsupportedResourcePage extends ListRecords
{
    use InteractsWithTestInertia;

    protected static string $resource = PostResource::class;

    public function mount(): void
    {
        abort(403);
    }
}
