<?php

namespace Filament\Tests\Panels\Fixtures\Resources\PostResource\Pages;

use Filament\Resources\Components\Tab;
use Filament\Tests\Models\Post;
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
