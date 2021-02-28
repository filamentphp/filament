<?php

namespace Filament\Pages;

use Filament\NavigationItem;
use Illuminate\Support\Str;
use Livewire\Component;

class Page extends Component
{
    public static $icon = 'heroicon-o-document-text';

    public static $navigationLabel;

    public static $navigationSort = 0;

    public static $slug;

    public static $view;

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

    public static function getNavigationLabel()
    {
        if (static::$navigationLabel) return static::$navigationLabel;

        return (string) Str::of(class_basename(static::class))
            ->kebab()
            ->replace('-', ' ');
    }

    public static function getNavigationSort()
    {
        return static::$navigationSort;
    }

    public static function getSlug()
    {
        if (static::$slug) return static::$slug;

        return (string) Str::of(class_basename(static::class))->kebab();
    }

    public static function getTitle()
    {
        if (property_exists(static::class, 'title')) return static::$title;

        return (string) Str::of(class_basename(static::class))
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    public static function navigationItems()
    {
        return [
            NavigationItem::make(Str::title(static::getNavigationLabel()), static::generateUrl())
                ->activeRule(
                    (string) Str::of(parse_url(static::generateUrl(), PHP_URL_PATH))
                        ->after('/')
                        ->append('*'),
                )
                ->icon(static::getIcon())
                ->sort(static::getNavigationSort()),
        ];
    }

    public static function route()
    {
        return Route::make(static::getSlug(), static::getSlug());
    }

    public function notify($message)
    {
        $this->dispatchBrowserEvent('notify', $message);
    }

    public function render()
    {
        return view(static::$view, $this->getViewParameters())
            ->layout('filament::components.layouts.app');
    }

    public function getViewParameters()
    {
        return array_merge($this->viewParameters(), [
            'title' => static::getTitle(),
        ]);
    }

    public function viewParameters()
    {
        return [];
    }
}
