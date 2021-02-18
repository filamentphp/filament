<?php

namespace Filament;

use Filament\Components\Concerns\HasTitle;
use Filament\Components\Concerns\SendsToastNotifications;
use Filament\Pages\AuthorizationManager;
use Filament\Pages\Route;
use Illuminate\Support\Str;
use Livewire\Component;

class Page extends Component
{
    use HasTitle;
    use SendsToastNotifications;

    public static $icon = 'heroicon-o-document-text';

    public static $label;

    public static $slug;

    public static $sort = 0;

    protected static $view;

    public static function authorization()
    {
        return [];
    }

    public static function authorizationManager()
    {
        return new AuthorizationManager(static::class);
    }

    public static function generateUrl($parameters = [], $absolute = true)
    {
        return route('filament.pages.' . static::route()->name, $parameters, $absolute);
    }

    public static function getIcon()
    {
        return static::$icon;
    }

    public static function getLabel()
    {
        if (static::$label) return static::$label;

        return (string) Str::of(class_basename(static::class))
            ->kebab()
            ->replace('-', ' ');
    }

    public static function getSlug()
    {
        if (static::$slug) return static::$slug;

        return (string) Str::of(class_basename(static::class))->kebab();
    }

    public static function getSort()
    {
        return static::$sort;
    }

    protected function getViewParameters()
    {
        return array_merge($this->viewParameters(), [
            'title' => static::getTitle(),
        ]);
    }

    public static function navigationItems()
    {
        return [
            NavigationItem::make(Str::title(static::getLabel()), static::generateUrl())
                ->activeRule((string) Str::of(parse_url(static::generateUrl(), PHP_URL_PATH))
                    ->after('/')
                    ->append('*'),
                )
                ->icon(static::getIcon())
                ->sort(static::getSort()),
        ];
    }

    public static function route()
    {
        return Route::make(static::getSlug(), static::getSlug());
    }

    protected function viewParameters()
    {
        return [];
    }

    public function render()
    {
        return view(static::$view, $this->getViewParameters())
            ->layout('filament::components.layouts.app');
    }
}
