<?php

namespace Filament\Models;

use Filament\Http\Livewire\Dashboard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Sushi\Sushi;

class NavigationItem extends Model
{
    use Sushi;

    public function getRows()
    {
        $items = collect();

        $items->push([
            'class' => Dashboard::class,
            'active' => 'filament.dashboard',
            'icon' => 'heroicon-o-home',
            'label' => __('filament::dashboard.title'),
            'sort' => -1,
            'url' => route('filament.dashboard'),
        ]);

        Resource::all()
            ->filter(fn ($resource) => $resource->authorizationManager()->can($resource->router()->getIndexRoute()->action))
            ->map(function ($resource) use (&$items) {
                $url = $resource::route();

                $items->push([
                    'class' => $resource->className,
                    'active' => (string) Str::of(parse_url($url, PHP_URL_PATH))->after('/')->append('*'),
                    'icon' => $resource->icon,
                    'label' => (string) Str::of($resource->label)->plural()->title(),
                    'sort' => $resource->sort,
                    'url' => $url,
                ]);
            });

        return $items
            ->sortBy('label')
            ->sortBy('sort')
            ->toArray();
    }
}
