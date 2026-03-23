<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Filament\Resources\PostResource\Widgets\PostStatsOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        if (! request()->query('headerWidgets')) {
            return [];
        }

        return [
            PostStatsOverview::class,
        ];
    }

    public function getTabs(): array
    {
        $tabStyle = request()->query('tabStyle');

        if (! $tabStyle) {
            return [];
        }

        if ($tabStyle === 'icons') {
            return [
                'all' => Tab::make()
                    ->icon(Heroicon::Bars3),
                'published' => Tab::make()
                    ->icon(Heroicon::CheckCircle)
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'published')),
                'draft' => Tab::make()
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'draft')),
                'reviewing' => Tab::make()
                    ->icon(Heroicon::OutlinedClock)
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'reviewing')),
            ];
        }

        if ($tabStyle === 'badgeColors') {
            return [
                'all' => Tab::make()
                    ->badge(8),
                'published' => Tab::make()
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'published'))
                    ->badge(6)
                    ->badgeColor('success'),
                'draft' => Tab::make()
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'draft'))
                    ->badge(1)
                    ->badgeColor('gray'),
                'reviewing' => Tab::make()
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'reviewing'))
                    ->badge(1)
                    ->badgeColor('warning'),
            ];
        }

        return [
            'all' => Tab::make()
                ->badge(8),
            'published' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'published'))
                ->badge(6),
            'draft' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'draft'))
                ->badge(1),
            'reviewing' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'reviewing'))
                ->badge(1),
        ];
    }
}
