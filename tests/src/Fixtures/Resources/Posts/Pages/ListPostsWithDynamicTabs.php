<?php

namespace Filament\Tests\Fixtures\Resources\Posts\Pages;

use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Database\Eloquent\Builder;

class ListPostsWithDynamicTabs extends ListPosts
{
    public function getTabs(): array
    {
        $tabs = ['all' => Tab::make('All posts')];

        foreach (Post::query()->get() as $post) {
            $tabs[$post->title] = Tab::make('Stored category')
                ->extraAttributes(['data-testid' => 'stored-tab-' . $post->getKey()])
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereKey($post->getKey()));
        }

        return $tabs;
    }
}
