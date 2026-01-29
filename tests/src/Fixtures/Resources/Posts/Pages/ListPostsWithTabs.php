<?php

namespace Filament\Tests\Fixtures\Resources\Posts\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;

class ListPostsWithTabs extends ListRecords
{
    protected static string $resource = PostResource::class;

    public bool $shouldExcludeTabQueryWhenResolvingRecord = true;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $published = Tab::make('Published')
            ->modifyQueryUsing(fn ($query) => $query->where('is_published', true));

        $draft = Tab::make('Draft')
            ->modifyQueryUsing(fn ($query) => $query->where('is_published', false));

        if ($this->shouldExcludeTabQueryWhenResolvingRecord) {
            $published->excludeQueryWhenResolvingRecord();
            $draft->excludeQueryWhenResolvingRecord();
        }

        return [
            'published' => $published,
            'draft' => $draft,
        ];
    }
}
